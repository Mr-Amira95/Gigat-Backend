Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('tags', TagController::class);
}); 