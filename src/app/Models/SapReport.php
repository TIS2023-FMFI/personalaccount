<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class SapReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'path',
        'uploaded_on'
    ];

    /**
     * Indicates if the model should be timestamped, using created_at and updated_at columns.
     *
     * @var mixed
     */
    public $timestamps = false;

    /**
     * Get the account with which the report is associated.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Generate a name for the SAP report file represented by the model.
     * 
     * @throws FileNotFoundException
     * thrown if the file associated with the model was be found
     * @return string
     * the generated file name
     */
    public function generateReportFileName()
    {
        $mimeType = Storage::mimeType($this->path);

        if ($mimeType === false) {
            throw new FileNotFoundException();
        }

        $extension = MimeType::search($mimeType);
        
        $sanitizedSapId = $this->account->getSanitizedSapId();
        $contentClause = 'sap-repport';
        $uploadedOn = Date::parse($this->uploaded_on)->format('d-m-Y');

        return "${sanitizedSapId}_${contentClause}_${uploadedOn}.${extension}";
    }
}
