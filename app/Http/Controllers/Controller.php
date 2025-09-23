<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public const SUCCESS_MESSAGE = 'Request processed successfully!';
    public const FAILED_MESSAGE = 'Unable to process the request. Please try again!';
    public const EXCEPTION_MESSAGE = 'Exception occured. Please try again!';
    public const INVALID_CREDENTIALS_MESSAGE = 'Invalid credentials';
    public const LOGOUT_SUCCESS_MESSAGE = 'Logged out successfully!';
    public const UPDATE_SUCCESS_MESSAGE = 'Updated Succesfully!';
    public const DELETE_SUCCESS_MESSAGE = 'Deleted Succesfully!';


    public const SUBSCRIPTION_CANCELLED_MESSAGE = 'Subscription Cancelled.';
    public const NO_ACTIVE_SUBSCRIPTION_MESSAGE = 'No active subscription.';

    public const SUCCESS_STATUS = 'success';
    public const ERROR_STATUS = 'error';

    public const SUCCESS = 200;
    public const ERROR = 500;
    public const INVALID = 422;
}
