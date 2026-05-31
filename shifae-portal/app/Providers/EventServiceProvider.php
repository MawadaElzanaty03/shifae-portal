<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// هنا نقوم باستدعاء الكلاسات الصحيحة لمشروعك
use App\Models\Booking; 
use App\Observers\BookingObserver;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        try {
            // ربط نموذج الحجز بالمراقب الخاص به (بدل Appointment)
            Booking::observe(BookingObserver::class);
        } catch (\Exception $exceptionError) {
            Log::error('Error booting EventServiceProvider: ' . $exceptionError->getMessage());
        }
    }
}