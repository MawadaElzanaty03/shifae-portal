<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Booking;

class ReportController extends Controller
{
    /**
     * واجهة التقارير السنوية للإدارة
     */
    public function annualReport(Request $request)
    {
        // التحقق من أن السنة المدخلة صحيحة
        $request->validate([
            'year' => 'nullable|numeric|digits:4|min:2000|max:' . (\Carbon\Carbon::now()->year + 1),
        ], [
            'year.numeric' => 'يجب أن تكون السنة رقماً.',
            'year.digits' => 'السنة يجب أن تتكون من 4 أرقام.',
            'year.max' => 'لا يمكنك استخراج تقرير لسنة بعيدة في المستقبل.'
        ]);

        try {
            // جلب السنة من المستخدم او استخدام العام الحالي افتراضيا اذا لم يذكر
            $year = $request->input('year', \Carbon\Carbon::now()->year);
            
            // جلب الارقام من النماذج  حسب السنة المحددة
            $genderStats = Patient::getGenderStatistics($year); 
            $ageStats = Patient::getAgeStatistics($year);       
            $busiestMonths = Booking::getBusiestMonths($year);  
           
            // حساب النسبة المئوية لذكور و الاناث
            $totalGender = $genderStats['maleCount'] + $genderStats['femaleCount'];
            $maleRatio = $totalGender > 0 ? round(($genderStats['maleCount'] / $totalGender) * 100) : 0;
            $femaleRatio = $totalGender > 0 ? round(($genderStats['femaleCount'] / $totalGender) * 100) : 0;
           
            // حساب النسبة المئوية للاطفال و الكبار
            $totalAge = $ageStats['childrenCount'] + $ageStats['adultsCount'];
            $childrenRatio = $totalAge > 0 ? round(($ageStats['childrenCount'] / $totalAge) * 100) : 0;
            $adultsRatio = $totalAge > 0 ? round(($ageStats['adultsCount'] / $totalAge) * 100) : 0;
         
            return view('reports.annual', compact(
                'year',
                'genderStats', 'maleRatio', 'femaleRatio',
                'ageStats', 'childrenRatio', 'adultsRatio',
                'busiestMonths'
            ));

        } catch (\Exception $exceptionError) {
            // في حال حدوث خطأ نرجعه مع رسالة
            \Illuminate\Support\Facades\Log::error('Error generating annual report: ' . $exceptionError->getMessage());
            return back()->withErrors(['systemError' => 'حدث خطأ غير متوقع أثناء توليد التقرير السنوي.']);
        }

    }
}
