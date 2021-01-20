<?php

namespace App\Http\Controllers;

// Menggunakan 1 line use
use App\{Category, Post, Tag};
// 3 line use
// use App\Category;
// use App\Post;
// use App\Tag;

use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
    * this method will always run first if you call one of below method
    *
    * @return void
    */
    public function __construct()
    {
        // load middleware auth, cek semua method kecuali method index
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * fungsi untuk menampilkan posts
     *
     * @return void
     */
    public function index(){
        // mengambil semua data
        // return Post::get();
        // mengambil atribut titlenya aja
        // return Post::get(['title', 'slug']);

        // $posts = Post::get(); // mengambil semua data post
        // $posts = Post::limit(2)->get(); // mengambil data post dengan dilimit 2 post saja

        /* ------------------------------- pagination ------------------------------- */
        $posts = Post::latest()->paginate(6); // mengambil data post dengan paginasi 2 post disetiap request
        // $posts = Post::simplePaginate(2); // mengambil data post dengan paginasi 2 post disetiap request, dengan pagination next dan previous
        return view('post.index', ['posts' => $posts]);
    }

    /**
     * fungsi untuk menampilkan post
     *
     * @param  mixed $post
     * @return void
     */
    public function show(Post $post){
        // melihat nilai variable slug
        // return ($slug);

        // mengambil data dari database dengan basic DB class, perlihatkan backslash untuk keluar dari namespace PostController
        // $post = \DB::table('posts')->where('slug', $slug)->first();
        // untuk menampilkan variable dan menghentikan proses server side execution
        // dd($post);

        // menggunakan model post
        // $post = \App\Post::where('slug', $slug)->first();

        /* -------------------------------------------------------------------------- */
        /*                mengecek apa suatu variabel kosong atau tidak               */
        /* -------------------------------------------------------------------------- */
        // dengan function is_null
        // if(is_null($post)){
        //     abort(404, 'Tidak ditemukan');
        // }
        // dengan true false if
        // if(!$post){
        //     abort(404);
        // }
        // dengan firtsOrFail(), memanggil langsung 404 jika tidak ada
        // $post = Post::where('slug', $slug)->firstOrFail();

        // mengirim slug ke view
        return view('post.show', compact('post'));
    }

/* ---------------------- apabila ada 2 url identifier ---------------------- */
    // public function show(Category $category, Post $post){

    // }

    /**
     * fungsi untuk menmpilakan form post creator
     *
     * @return void
     */
    public function create()
    {
        $button = '+ Create';
        $post = new Post;
        $categories = Category::get();
        $tags = Tag::get();
        return view('post.create', compact('post', 'button', 'categories', 'tags'));
    }

    /**
     * fungsi untuk menyimpan post
     *
     * @param  mixed $request
     * @return void
     */
    public function store(PostRequest $request)
    {
        /* ------------------------ menambahkan validasi form ----------------------- */
        // cara pertama
        // $this->validate($request, [
        //     'title' => 'required|min:3',
        //     'body' => 'required'
        // ]);
        // cara kedua
        // $request->validate([
        //     'title' => 'required|min:3',
        //     'body' => 'required'
        // ]);

        /* -------------- siapkan variabel dan simpan ke dalam database ------------- */
        // $post = new Post;
        // $post->title = $request->title; // The fiirst post
        // $post->slug = \Str::slug($request->title); // the-first-post
        // $post->body = $request->body;
        // $post->save();
        /* -------------------------------------------------------------------------- */


        /* ----- menggunakan class dan mengimplementasikan guarded dan fillable ----- */
        /**
         * jika menggunakan cara ini harus menambahkan variabel
         *
         * protected $fillable // untuk mendefinisikan attribut yang dapat diisi oleh user
         * atau
         * protected $guarded // untuk mendefinisikan attribut yang TIDAK dapat diisi oleh user
         *
         * pada model Post;
         */
        // cara sederhana
        // Post::create([
        //     'title' => $request->title, // the first post
        //     'slug' => \Str::slug($request->title), // the-first-post
        //     'body' => $request->body
        // ]);
        /* ---------------------------- cara lebih simpel --------------------------- */
        /**
         * PERINGATAN?!
         * Apabila menggunakan cara ini harap perhatikan variabel protected $fillable
         * atau $guarded, agar user tidak bisa seenaknya memasukkan data ke salah
         * satu attribut dalam tabel kita.
         */
        // $posts = $request->all();
        // $posts['slug'] = \Str::slug($request->title);
        // Post::create($posts);

        // redirect ke halaman post
        // return redirect()->to('post');

        // menggunakan function back untuk redirect
        // return redirect()->to('post/create');

        /* -------------------------------------------------------------------------- */
        /*                             THE SIMPLEST METHOD                            */
        /* -------------------------------------------------------------------------- */
        /* ------------------------------ cara pertama ------------------------------ */
        // -> menggunakan variable validate dengan bantuan class request yang dialiaskan ke variabel $request
        // validasinya
        // $attr = $request->validate([
        //     'title' => 'required|min:3',
        //     'body' => 'required'
        // ]);

        // generate slugnya
        // $attr['slug'] = \Str::slug($request->title);

        // store ke database
        // Post::create($attr);

        // cara kedua -> menggunakan function request
        // $attr = request()->validate([
        //     'title' => 'required|min:3',
        //     'body' => 'required'
        // ]);
        // $attr = $this->validateRequest();

        // $attr['slug'] = \Str::slug(request('title'));
        // Post::create($attr);
        /* -------------------------------------------------------------------------- */

        /* ---------------- menggunakan request method untuk validasi ---------------- */
        // $request = $this->validateRequest();

        // $request['slug'] = \Str::slug(request('title'));
        // Post::create($request);
        /* ---------------- menggunakan request class untuk validasi ---------------- */
        // masukan semua request ke attr
        $attr = $request->all();
        // apabila menggunakan bawaan dari eloquent, nama attributnya harus category id, jika tidak set manual kyk gini
        $attr['id_category'] = $attr['category'];

        // buat slug title
        $attr['slug'] = \Str::slug($request->input('title'));

        // simpan ke database
        $post = Post::create($attr);

        // cara attach tags ke post yaitu dengan
        $post->tags()->attach(request('tags'));

        // Buat session flash untuk notifikasi
        session()->flash('success', 'The Post was created');

        // return back();
        return redirect()->to('post');
    }

    /**
     * fungsi untuk menampilkan form edit post
     *
     * @param  mixed $post
     * @return void
     */
    public function edit(Post $post){
        $button = 'Update';
        $categories = Category::get();
        $tags = Tag::get();
        return view('post.edit', compact('post', 'button', 'categories', 'tags'));
    }

    /**
     * melakukan update form
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(PostRequest $request, Post $post)
    {
        /* ------------------- validasi menggunakan method berbeda ------------------ */
        // validasi form
        // $attr = $this->validateRequest();

        /* ------------------- validasi menggunakan kelas request ------------------- */
        $attr = $request->all();
        $attr['id_category'] = request('category'); // update category

        // update ke database
        $post->update($attr);
        // cara update tags ke post yaitu dengan
        $post->tags()->sync(request('tags'));

        // buat session flash untuk notifikasi
        session()->flash('success', 'The Post was updated');

        // kembalikan ke halaman sebelumnya
        return redirect()->to('post');
        // return back();
    }

    /**
     * fungsi untuk menghapus post
     *
     * @return void
     */
    public function destroy(Post $post){
        $post->tags->detach(); // untuk menghapus tagsnya
        $post->delete();
        session()->flash('success', 'The post was successfully deleted');
        return redirect()->to('/post');
    }

/* -------------------------------------------------------------------------- */
/*                               OTHER FUNCTION                               */
/* -------------------------------------------------------------------------- */

    // melakukan request dengan method
    public function validateRequest(){
        return request()->validate([
            'title' => 'required|min:3',
            'body' => 'required'
        ]);
    }

}
