<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class FinancialOperation extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Array of attributes excluded from mass assignation.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Array of related tables which should be eager-loaded from the DB along with this model.
     *
     * @var string[]
     */
    protected $with = ['operationType'];


    /**
     *  Extends a query asking for financial operations so that it demands only operations which represent expenses,
     *  and returns the new query builder.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpenses(Builder $query): Builder
    {
        return $query
            ->join('operation_types', 'operation_type_id','=','operation_types.id')
            ->where('expense', '=', true);
    }

    /**
     *  Extends a query asking for financial operations so that it demands only operations which represent income,
     *  and returns the new query builder.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIncomes(Builder $query): Builder
    {
        return $query
            ->join('operation_types', 'operation_type_id','=','operation_types.id')
            ->where('expense', '=', false);
    }

    /**
     * Returns the account to which this operation belongs.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Returns the ID of the user who owns this operation's account.
     *
     * @return mixed
     */
    public function getUserId(){
        return $this->account->getUserId();
    }

    /**
     * Returns the type of this operation.
     *
     * @return BelongsTo
     */
    public function operationType()
    {
        return $this->belongsTo(OperationType::class);
    }

    /**
     * Returns whether this operation is an expense (whether it represents a negative sum).
     *
     * @return bool
     */
    public function isExpense(): bool
    {
        return $this->operationType->expense;
    }

    /**
     * Returns whether this operation is a lending
     * (based on the operation type, not on existence of a DB 'lending' record).
     *
     * @return bool
     */
    public function islending(): bool
    {
        return $this->operationType->isLending();
    }


    /**
     * Returns the lending record related to this operation, if it exists.
     *
     * @return HasOne
     */
    public function lending(){
        return $this->hasOne(Lending::class, 'id', 'id');
    }

    /**
     * Attempts to delete the file stored on the 'attachment' path.
     */
    public function deleteAttachmentIfExists()
    {
        $attachment = $this->attachment;
        if ($attachment && Storage::exists($attachment)) Storage::delete($attachment);
    }

    /**
     * Returns an array of data needed for the CSV export.
     *
     * @return array
     */
    public function getExportData(){
        return [
            $this->id,
            $this->account->sap_id,
            $this->title,
            $this->date,
            $this->operationType->name,
            $this->subject,
            $this->getSumString(),
            $this->attachment,
            $this->getCheckedString(),
            $this->sap_id
        ];
    }

    /**
     * Returns a string containing this operation's sum, with '-' after the number if it's an expense.
     *
     * @return string
     */
    public function getSumString(){
        $string = sprintf("%.2f", $this->sum);
        if ($this->isExpense()) $string .= "-";
        return $string;
    }

    /**
     * Returns whether this operation is checked, in form of an all-caps string,
     * or an empty string if this is a lending.
     *
     * @return string
     */
    public function getCheckedString(){
        if ($this->isLending()) return '';
        return $this->checked ? 'TRUE' : 'FALSE';
    }

}
