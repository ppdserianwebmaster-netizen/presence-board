<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_MEETING = 'meeting';
    public const TYPE_COURSE = 'course';
    public const TYPE_TRAINING = 'training';
    public const TYPE_TRAVEL = 'travel';
    public const TYPE_LEAVE = 'leave';
    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_MEETING => 'Meeting',
        self::TYPE_COURSE => 'Course',
        self::TYPE_TRAINING => 'Training',
        self::TYPE_TRAVEL => 'Travel',
        self::TYPE_LEAVE => 'Leave',
        self::TYPE_OTHER => 'Other',
    ];

    public const STATUS_PLANNED = 'planned';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['user_id','start_datetime','end_datetime','type','note','status'];
    protected $casts = ['start_datetime'=>'datetime','end_datetime'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeActiveNow(Builder $query): void
    {
        $now = now();
        $query->whereIn('status',[self::STATUS_PLANNED,self::STATUS_ACTIVE])
            ->where('start_datetime','<=',$now)
            ->where(function($q) use($now){$q->whereNull('end_datetime')->orWhere('end_datetime','>=',$now);});
    }

    public function scopeOfType(Builder $query,string $type): void {$query->where('type',$type);}
    public function scopeWhereStatus(Builder $query,string $status): void {$query->where('status',$status);}
    public function scopeOverlaps(Builder $query,Carbon $start,Carbon $end): void
    {
        $query->where(function($q) use($start,$end){
            $q->where('start_datetime','<',$end)
              ->where(function($q2) use($start){$q2->whereNull('end_datetime')->orWhere('end_datetime','>',$start);});
        });
    }
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('end_datetime','>=',Carbon::now()->startOfDay())
              ->whereIn('status',[self::STATUS_PLANNED,self::STATUS_ACTIVE]);
    }
}
