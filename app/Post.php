<?php

/**
 * model class pada laravel (eloquent)
 * - dengan menamai nama model dengan aturan, nama tabel plural (cth: posts) dan nama tabel singular (cth: Post), maka model
 *   kosong bisa langsung dieksekusi query buildernya tanpa harus mengidentifikasi nama tabelnya terlebih dahulu.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // wajib menggunakan salah satu dari variabel ini
    protected $fillable = ['title', 'body', 'slug']; // menandakan attribut apa saja yg dapat diisi oleh controller
    // ATAU
    // protected $guarded = ['id', 'created_at', 'updated_at', 'edited_at']; // menandakan attribut apa saja yang tidak dapat diisi oleh controller
    /* -------------------------------------------------------------------------- */


    // default nama table dari model class laravel adalah bentuk plural dari nama model
    // protected $table = "posts"; // tidak perlu diaktifkan, karena sudah secara otomatis tanpa harus diidentifikasi

    // identifikasi manual nama tabel
    // protected $table = "post";

    // fungsi ini untuk membaca route key name dari URL apabila nama slug tidak diidentifikasi pada Route::get()
    public function getRouteKeyName()
    {
        return 'slug'; // kasih tau laravel bahwa route key name itu adalah slug dari kolom tabel posts
    }

    // membuat function untuk testing di Tinker dengan menambahkan kata depan scope pada method
    // jangan lupa untuk restart TInker setelah membuat method scope baru, supaya bisa terbaca pada Tinker
    // cara memanggil method ini yaitu dengan mengeksekusi command berikut : Post::latestFirst();
    public function scopeLatestFirst(){
        return $this->latest()->first();
    }
    public function scopeLatestPost(){
        return $this->latest()->get();
    }

/* -------------------------------------------------------------------------- */
/*                                relasi tabel                                */
/* -------------------------------------------------------------------------- */
    /**
     * untuk memberitahukan kalau post memiliki satu kategori
     * perhatikan untuk penggunaan kata dalam penamaan fungsi kategori menggunakan kata singular
     *
     * @return void
     */
    public function category()
    {
        // jika memiliki satu attribut lain
        // return $this->hasOne(Category::class);

        // jika ada has Many, maka seiyanya ada inversenya, untuk menampilkan hubungan keduanya
        // return $this->belongsTo(Category::class); // dengan defaultnya fk 'category_id'
        return $this->belongsTo(Category::class, 'id_category');
    }
}
