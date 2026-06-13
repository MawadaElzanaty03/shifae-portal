<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    use HasFactory;
    protected $primaryKey = 'documentId';
    protected $fillable = ['userId', 'documentName', 'filePath'];

    public function user() {
        return $this->belongsTo(User::class, 'userId', 'userId');
    }
}
