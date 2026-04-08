<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelItineraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id', 'sequence', 'journey_date', 'journey_time',
        'from_location', 'to_location', 'travel_mode', 'carrier_name',
        'booking_reference', 'seat_class', 'cost', 'notes'
    ];

    protected $casts = [
        'journey_date' => 'date',
        'cost' => 'decimal:2',
        'sequence' => 'integer',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }
}
