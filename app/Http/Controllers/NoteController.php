<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
      
       $notes = Note::whereBelongsTo(Auth::user())->latest('updated_at')->paginate(5);
     
       return view('notes.index')->with('notes',$notes);
        $notes ->each(function($note){
        dump($note->title);
       });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('notes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Note $note)
    {
        
       
      $path_file= $request->file('file_name')->store('pdf_files','public');
      print_r($path_file);
        $files_data=$request->files;
        $request->validate([
            'title' => 'required|max:120'
            
        ]);


      //  foreach ($files_data as $key => $value) {
           // print_r('inside');
      //     echo "<pre>";
       //     $details =(array)$value;
       //    print_r($details);
       //     echo "<pre>";
      //  }
     
        Note::create([
            'user_id' => Auth::id(),
            'uuid' => Str::uuid(),
            'title' => $request->title,
            'file' => $path_file
        ]);
        return to_route('notes.index');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        

        if(!$note->user->is(Auth::user())) {
            return abort(403);
        }
       //  $note =Note::where('uuid',$uuid)->where('user_id',Auth::id())->firstorfail();
         return view('notes.show')->with('note',$note);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Note $note)
    {
        
        if($note->user_id != Auth::id()) {
            return abort(403);
        }
      
         return view('notes.edit')->with('note',$note);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Note $note)
    {
     
       
     
        $request->validate([
            'title' => 'required|max:120',
            'text' => 'required'
        ]);

        DB::table('notes')->update([
           
            'title' => $request->title,
            'text' => $request->text
        ]);
        return to_route('notes.show', $note)->with('success','Note updated successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        //deleting here
     

        $note->delete();

        return to_route('notes.index')->with('success', 'Note moved to trash');
    }


    
}
