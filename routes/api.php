<?php

use App\Http\Controllers\AdminConstroller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Adminer;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\FreelancersController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectApplicantsController;
use App\Http\Controllers\ProjectCommentsController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\reportController;
use App\Http\Controllers\VerificationController;
use App\Models\Admin;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/ViewFreelancerRating/{id}',[RatingController::class,'ViewFreelancerRating']);
Route::post('/registerAdmin',[AdminController::class,'registerAdmin']);


Route::post('/create-cardholder', 'PaymentController@createCardholder');
Route::post('/create-card', 'PaymentController@createCard');
Route::post('/create-cardholder-and-card', 'PaymentController@createCardholderAndCard');


Route::get('/checkPaymentStatus', [PaymentController::class,'checkPaymentStatus'])->name('checkPaymentStatus');
Route::get('/payment/success', [PaymentController::class,'paymentSuccess']);
Route::get('/payment/cancel', [PaymentController::class,'paymentCancel']);


// Route for receiving payment from Stripe
Route::post('/receive-payment', [PaymentController::class, 'receivePayment']);

//Project

Route::get('/ShowAll/{search?}',[ProjectsController::class,'ShowAll']);
Route::get('/showProject_Category/{category}',[ProjectsController::class,'showProject_Category']);
Route::get('/showAllProjectsDesc/{search?}',[ProjectsController::class,'showAllProjectsDesc']);
Route::get('/showProjectById/{id}',[ProjectsController::class,'showProjectById']);


//Client
Route::get('/ShowClientBYID/{id}',[ProjectsController::class,'ShowClientBYID']);



//freelancers
Route::get('/showAllFreelancer',[FreelancersController::class,'showAllFreelancer']);
Route::get('/showFreelancers_Category',[FreelancersController::class,'showAllFreelancer']);
Route::get('/searchFreelancer/{search?}/{AsWhat?}',[FreelancersController::class,'searchFreelancer']);
Route::get('/ShowFreelancerByID/{id}',[FreelancersController::class,'ShowFreelancerByID']);


//show picture and show file
Route::get('/showFile/{id}',[FreelancersController::class,'showFile']);
Route::get('/showpic/{id}',[FreelancersController::class,'showPicture']);


//verification Email
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');


//forget password
Route::post('/forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/reset-password', 'App\Http\Controllers\Auth\ResetPasswordController@reset');



// public Login and Register
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//Show All comments to a certian user
Route::get('/showAllComments/{id}',[ProjectCommentsController::class,'showAllComments']);

//Route::get('/posts', [PostsController::class,'index']);
// private routes that need authentication and email verification


Route::group(['middleware'=> ['auth:sanctum', 'verified']],function(){

    //Edit Profile
   Route::post('/changepassword',[AuthController::class,'changepassword']);
   Route::post('/changeName',[AuthController::class,'changeName']);




    //Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    //FREELANCER
    Route::post('/addfreelancer',[FreelancersController::class,'storeFreelancer']);
    Route::get('/FreelancerInfo',[FreelancersController::class,'show']);
    Route::post('/deleteFreelancer',[FreelancersController::class,'deleteFreelancer']);
    Route::get('/ViewRating',[RatingController::class,'ViewRating']);


    //CLIENT
    Route::post('/UpdateClient',[ClientsController::class,'UpdateClient']);

    //PROJECT
    Route::post('/Addproject',[ProjectsController::class,'Addproject']);
    Route::post('/deleteProject',[ProjectsController::class,'deleteProject']);
    Route::post('/updateProject/{id}',[ProjectsController::class,'updateProject']);
    Route::get('/showMyProjects',[ProjectsController::class,'showMyProjects']);
    Route::post('/Done/{id}',[ProjectsController::class,'Done']);
    Route::get('/getAppliedProjects',[ProjectsController::class,'getAppliedProjects']);


    //payment
    Route::post('/createPayment', [PaymentController::class,'createPayment']);
    Route::post('payout', [PaymentController::class,'createPayout'])->name('payout.create');
    Route::post('showPayoutForm', [PaymentController::class,'showPayoutForm']);
    Route::post('/linkAccount',[PaymentController::class,'linkAccount']);




    //Comment
    Route::post('/AddComment/{id}',[ProjectCommentsController::class,'AddComment']);
    Route::post('/UpdateComment/{id}',[ProjectCommentsController::class,'UpdateComment']);
    Route::post('/deletComment/{id}',[ProjectCommentsController::class,'deletComment']);


    //Applications
    Route::post('/Addapplication/{id}',[ProjectApplicantsController::class,'Addapplication']);
    Route::post('/UpdateApplication',[ProjectApplicantsController::class,'UpdateApplication']);
    Route::post('/AcceptApplication/{id}',[ProjectApplicantsController::class,'AcceptApplication']);
    Route::post('/RejectApplication/{id}',[ProjectApplicantsController::class,'RejectApplication']);
    Route::get('/showMyApplications',[ProjectApplicantsController::class,'showMyApplications']);
    Route::post('/deleteApplication/{id}',[ProjectApplicantsController::class,'deleteApplication']);
    Route::get('/showAppliedApplications',[ProjectApplicantsController::class,'showAppliedApplications']);

//Route::get('/posts/client_posts',[PostsController::class,'client_posts']);


    //edit profile ta3olet alaa
    Route::post('/editProfile',[ClientsController::class,'editProfile']);

    //Conversations
    Route::get('/ShowConversations',[Conversation::class,'ShowConversations']);



    //Reports
    Route::post('/ReportFreelancer/{id}', [reportController::class,'ReportFreelancer']);
    Route::post('/ReportProject/{id}', [reportController::class,'ReportProject']);
    Route::post('/ReportComment/{id}', [reportController::class,'ReportComment']);



    //Rating
    Route::post('/AddRating/{id}', [RatingController::class,'AddRating']);



});

Route::group(['middleware' => 'auth:admin'], function () {
    // Admin-only routes
    Route::post('/BanUser/{id}', [AdminController::class,'BanUser']);
    Route::post('/unBanUser/{id}', [AdminController::class,'unBanUser']);
    Route::post('/Freeze/{id}', [AdminController::class,'Freeze']);
    Route::post('/unFreeze/{id}', [AdminController::class,'unFreeze']);
    Route::post('/delete/{id}', [AdminController::class,'deleteUser']);
    Route::post('/deleteProjectBYAdmin/{id}', [AdminController::class,'deleteProjectBYAdmin']);
    //zedon 3ala postman
    Route::post('/DoneProjectByAdmin/{id}', [AdminController::class,'DoneProjectByAdmin']);
    Route::post('/SearchByusername/{username}', [AdminController::class,'SearchByusername']);
    Route::get('/VeiwbyuserID/{id}', [AdminController::class,'VeiwbyuserID']);

    //Reports
    Route::post('/DeleteReportFreelancer/{id}', [AdminController::class,'DeleteReportFreelancer']);
    Route::post('/ViewReportFreelancer/{id}', [AdminController::class,'ViewReportFreelancer']);
    Route::post('/showReportedFreelancers', [AdminController::class,'showReportedFreelancers']);
    Route::post('/DeleteReportProject/{id}', [AdminController::class,'DeleteReportProject']);
    Route::post('/ViewReportProject/{id}', [AdminController::class,'ViewReportProject']);
    Route::post('/showReportedProjects', [AdminController::class,'showReportedProjects']);
    Route::post('/DeleteCommentProject/{id}', [AdminController::class,'DeleteCommentProject']);
    Route::post('/ViewCommentProject/{id}', [AdminController::class,'ViewCommentProject']);
    Route::post('/showCommentProjects', [AdminController::class,'showCommentProjects']);

});
Route::post('/loginAdmin', [AdminController::class, 'loginAdmin']);
Route::post('/registerAdmin', [AdminController::class, 'registerAdmin']);


