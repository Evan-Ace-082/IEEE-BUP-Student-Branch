<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\CommitteeController as AdminCommitteeController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\LeadershipProfileController as AdminLeadershipController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResearchPaperController as AdminResearchPaperController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\PendingAccountController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Member\AchievementController as MemberAchievementController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\ProfileController;
use App\Http\Controllers\Member\RegistrationController as MemberRegistrationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\AchievementController;
use App\Http\Controllers\Public\AnnouncementController;
use App\Http\Controllers\Public\CommitteeController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\MemberDirectoryController;
use App\Http\Controllers\Public\MembershipController;
use App\Http\Controllers\Public\ResearchPaperController;
use App\Http\Controllers\Public\ResourceController;
use App\Http\Controllers\SuperAdmin\AdminUserController;
use App\Http\Controllers\SuperAdmin\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'account.active'])->group(function () {
    Route::get('/research-papers/submit', [ResearchPaperController::class, 'create'])->name('research-papers.create');
    Route::post('/research-papers', [ResearchPaperController::class, 'store'])->middleware('throttle:uploads')->name('research-papers.store');
});

Route::middleware('throttle:browse')->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/about', AboutController::class)->name('about');
    Route::get('/committee', [CommitteeController::class, 'index'])->name('committee.index');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->name('achievements.show');
    Route::get('/members', [MemberDirectoryController::class, 'index'])->name('members.index');
    Route::get('/members/{user}', [MemberDirectoryController::class, 'show'])->name('members.show');
    Route::get('/research-papers', [ResearchPaperController::class, 'index'])->name('research-papers.index');
    Route::get('/research-papers/{paper}/pdf', [ResearchPaperController::class, 'pdf'])->where('paper', '[A-Za-z0-9\-]+')->name('research-papers.pdf');
    Route::get('/research-papers/{paper}', [ResearchPaperController::class, 'show'])->where('paper', '[A-Za-z0-9\-]+')->name('research-papers.show');
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/{album}', [GalleryController::class, 'show'])->name('gallery.show');
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});

Route::post('/events/{event}/register', [EventController::class, 'register'])
    ->middleware('throttle:event-register')
    ->name('events.register');
Route::post('/membership', [MembershipController::class, 'apply'])
    ->middleware('throttle:membership-form')
    ->name('membership.apply');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact-form')
    ->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login-post');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:account-register');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:password-email')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/account/pending', PendingAccountController::class)->name('account.pending');
});

Route::middleware(['auth', 'verified', 'account.active'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware(['auth', 'verified', 'account.active', 'role:member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', MemberDashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->middleware('throttle:uploads')->name('profile.update');
    Route::get('/registrations', [MemberRegistrationController::class, 'index'])->name('registrations');
    Route::post('/registrations/{registration}/cancel', [MemberRegistrationController::class, 'cancel'])->name('registrations.cancel');
    Route::get('/achievements', [MemberAchievementController::class, 'index'])->name('achievements');
    Route::post('/achievements', [MemberAchievementController::class, 'store'])->middleware('throttle:uploads')->name('achievements.store');
});

Route::middleware(['auth', 'verified', 'account.active', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');

    Route::resource('events', AdminEventController::class)->except(['show']);
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/export', [AdminRegistrationController::class, 'export'])->name('registrations.export');
    Route::put('/registrations/{registration}', [AdminRegistrationController::class, 'update'])->name('registrations.update');

    Route::resource('announcements', AdminAnnouncementController::class)->except(['show']);
    Route::resource('achievements', AdminAchievementController::class)->except(['show']);

    Route::get('/gallery', [AdminGalleryController::class, 'index'])->name('gallery.index');
    Route::get('/gallery/create', [AdminGalleryController::class, 'create'])->name('gallery.create');
    Route::post('/gallery', [AdminGalleryController::class, 'store'])->middleware('throttle:uploads')->name('gallery.store');
    Route::get('/gallery/{album}/edit', [AdminGalleryController::class, 'edit'])->name('gallery.edit');
    Route::put('/gallery/{album}', [AdminGalleryController::class, 'update'])->middleware('throttle:uploads')->name('gallery.update');
    Route::delete('/gallery/{album}', [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::put('/gallery-items/{item}', [AdminGalleryController::class, 'updateCaption'])->name('gallery.caption');
    Route::delete('/gallery-items/{item}', [AdminGalleryController::class, 'destroyItem'])->name('gallery.items.destroy');

    Route::resource('resources', AdminResourceController::class)->except(['show'])->middlewareFor(['store', 'update'], 'throttle:uploads');
    Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}/edit', [AdminMemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}', [AdminMemberController::class, 'update'])->name('members.update');
    Route::post('/members/{member}/approve', [AdminMemberController::class, 'approve'])->name('members.approve');
    Route::post('/members/{member}/suspend', [AdminMemberController::class, 'suspend'])->name('members.suspend');

    Route::get('/committee', [AdminCommitteeController::class, 'index'])->name('committee.index');
    Route::post('/committee', [AdminCommitteeController::class, 'store'])->name('committee.store');
    Route::get('/committee/{committee}', [AdminCommitteeController::class, 'show'])->name('committee.show');
    Route::put('/committee/{committee}', [AdminCommitteeController::class, 'update'])->name('committee.update');
    Route::delete('/committee/{committee}', [AdminCommitteeController::class, 'destroy'])->name('committee.destroy');
    Route::post('/committee/{committee}/members', [AdminCommitteeController::class, 'storeMember'])->middleware('throttle:uploads')->name('committee.members.store');
    Route::put('/committee-members/{committeeMember}', [AdminCommitteeController::class, 'updateMember'])->middleware('throttle:uploads')->name('committee.members.update');
    Route::delete('/committee-members/{committeeMember}', [AdminCommitteeController::class, 'destroyMember'])->name('committee.members.destroy');
    Route::post('/positions', [AdminCommitteeController::class, 'storePosition'])->name('positions.store');
    Route::put('/positions/{position}', [AdminCommitteeController::class, 'updatePosition'])->name('positions.update');
    Route::delete('/positions/{position}', [AdminCommitteeController::class, 'destroyPosition'])->name('positions.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::put('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');

    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::resource('leadership', AdminLeadershipController::class)->except(['show'])->parameters(['leadership' => 'profile'])->middlewareFor(['store', 'update'], 'throttle:uploads');
    Route::get('/research-papers', [AdminResearchPaperController::class, 'index'])->name('research-papers.index');
    Route::get('/research-papers/{researchPaper}', [AdminResearchPaperController::class, 'show'])->name('research-papers.show');
    Route::put('/research-papers/{researchPaper}', [AdminResearchPaperController::class, 'update'])->middleware('throttle:uploads')->name('research-papers.update');
    Route::post('/research-papers/{researchPaper}/approve', [AdminResearchPaperController::class, 'approve'])->name('research-papers.approve');
    Route::post('/research-papers/{researchPaper}/reject', [AdminResearchPaperController::class, 'reject'])->name('research-papers.reject');
    Route::post('/research-papers/{researchPaper}/publish', [AdminResearchPaperController::class, 'publish'])->name('research-papers.publish');
    Route::post('/research-papers/{researchPaper}/unpublish', [AdminResearchPaperController::class, 'unpublish'])->name('research-papers.unpublish');
    Route::delete('/research-papers/{researchPaper}', [AdminResearchPaperController::class, 'destroy'])->name('research-papers.destroy');
    Route::get('/content', [ContentController::class, 'edit'])->name('content.edit');
    Route::put('/content', [ContentController::class, 'update'])->name('content.update');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/members.csv', [ReportController::class, 'exportMembers'])->name('reports.members');
    Route::get('/reports/registrations.csv', [ReportController::class, 'exportRegistrations'])->name('reports.registrations');
});

Route::middleware(['auth', 'verified', 'account.active', 'role:super_admin'])->prefix('super-admin')->name('super.')->group(function () {
    Route::get('/admins', [AdminUserController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [AdminUserController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminUserController::class, 'store'])->name('admins.store');
    Route::get('/admins/{adminUser}', [AdminUserController::class, 'show'])->name('admins.show');
    Route::get('/admins/{adminUser}/edit', [AdminUserController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{adminUser}', [AdminUserController::class, 'update'])->name('admins.update');
    Route::post('/admins/{adminUser}/activate', [AdminUserController::class, 'activate'])->name('admins.activate');
    Route::post('/admins/{adminUser}/suspend', [AdminUserController::class, 'suspend'])->name('admins.suspend');
    Route::delete('/admins/{adminUser}', [AdminUserController::class, 'destroy'])->name('admins.destroy');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->middleware('throttle:uploads')->name('settings.update');
    Route::get('/admin-activity', [ActivityLogController::class, 'index'])->name('activity');
});
