<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PulseSurveyController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\KeepingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\ExecutiveAnalyticsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin');
            case 'executive':
                return redirect()->route('dashboard');
            case 'management':
                return redirect()->route('dashboard');
            default:
                return redirect()->route('survey.start');
        }
    }
    
    return view('welcome');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/tutorial', function () {
    return view('tutorial');
});
Route::post('/send-survey', [SurveyController::class, 'sendInvitation'])->name('survey.send');
Route::get('/period', [SurveyController::class, 'period'])->name('survey.period');

Route::middleware(['auth'])->group(function () {
    //全社アンケート
    Route::get('/survey', [SurveyController::class, 'index'])->name('survey.index');
    Route::get('/survey/start', [SurveyController::class, 'start'])->name('survey.start');
    Route::post('/survey/save-draft', [SurveyController::class, 'saveDraft'])->name('survey.saveDraft');
    Route::post('/survey/submit', [SurveyController::class, 'store'])->name('survey.store');
    Route::get('/survey/complete', [SurveyController::class, 'complete'])->name('survey.complete');
    Route::post('/{survey}/reset', [SurveyController::class, 'resetAnswers'])->name('survey.reset');
    Route::get('/survey/scores/{surveyId}', [SurveyController::class, 'showScores'])->name('survey.scores');

    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    //analytics
    Route::get('/analytics', [AnalyticsController::class, 'showAnalytics'])->name('analytics');
    Route::get('/analytics_detail', [AnalyticsController::class, 'analyticsDetail'])->name('analytics_detail');

    //planning
    Route::get('/planning', [PlanningController::class, 'index'])->name('planning');
    Route::post('/planning/propose', [PlanningController::class, 'propose'])->name('planning.propose');
    Route::post('/planning/update', [PlanningController::class, 'update'])->name('planning.update');
    
    //tracking
    Route::post('/tasks', [TrackingController::class, 'storeTask'])->name('tasks.store');
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking', [TrackingController::class, 'insert'])->name('insert');
    Route::post('/delete', [TrackingController::class, 'delete'])->name('delete');
    
    Route::get('/unique', [SurveyController::class, 'showSurveyResult'])->name('unique');
    Route::post('/milestones/store-all', [PlanningController::class, 'storeAll'])->name('milestones.storeAll');

    // admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin/update-survey-period', [AdminController::class, 'updateSurveyPeriod'])->name('admin.update-survey-period');

    // パルスサーベイのルート
    Route::prefix('pulse-survey')->name('pulse-survey.')->group(function () {
        Route::get('/start/{survey}', [PulseSurveyController::class, 'start'])->name('start');
        Route::get('/complete', [PulseSurveyController::class, 'complete'])->name('complete');
        Route::get('/{survey}', [PulseSurveyController::class, 'index'])->name('index');
        Route::post('/{survey}', [PulseSurveyController::class, 'store'])->name('store');
        Route::post('/{survey}/auto-save', [PulseSurveyController::class, 'autoSave'])->name('auto-save');
    });

    //ログアウト
    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('/logout/confirm', [LogoutController::class, 'confirmLogout'])->name('logout.confirm');

    Route::get('/unique', [SurveyController::class, 'showSurveyResult'])->name('unique');
    Route::post('/milestones/store-all', [PlanningController::class, 'storeAll'])->name('milestones.storeAll');
    Route::post('/keeping/store-all', [KeepingController::class, 'storeAll'])->name('keeping.storeAll');
    Route::get('/keeping', [KeepingController::class, 'index'])->name('keeping');

    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('/logout/confirm', [LogoutController::class, 'confirmLogout'])->name('logout.confirm');
});



//executive
Route::middleware(['auth', 'role:executive'])->group(function () {
    Route::get('/executive/dashboard', [ExecutiveDashboardController::class, 'index'])->name('executive.dashboard');
    Route::get('/executive/analytics', [ExecutiveAnalyticsController::class, 'index'])->name('executive.analytics');
    Route::get('/executive/analytics/data', [ExecutiveAnalyticsController::class, 'getData'])->name('executive.analytics.data');
    Route::get('/executive/analytics_detail', [ExecutiveAnalyticsController::class, 'analyticsDetail'])->name('executive.analytics_detail');
});

require __DIR__.'/auth.php';

