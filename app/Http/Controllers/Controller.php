<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Africa CDC KnowledgeHub API",
 *     version="1.0.0",
 *     description="HTTP API for the Africa CDC Knowledge Hub (web and mobile clients). **URL base:** routes in this specification are rooted at `/api` (example: `GET /api/publications` → `{server}/api/publications`). **Auth:** call `POST /api/login` with email and password to receive a Laravel Passport access token; send `Authorization: Bearer {access_token}` on protected operations. **Optional Bearer** on some public GET routes enables personalised results (e.g. recommended publications). **Errors:** Laravel validation typically returns `422` with `message` and/or `errors`; many success payloads use `status` and `data` at the top level."
 * )
 *
 * @OA\Server(
 *     url=L5_OPENAPI_SERVER_URL,
 *     description="Origin used in Try it out — must match how clients reach the app (scheme + host + path prefix if any; no trailing slash). Override with `APP_URL` or `L5_OPENAPI_SERVER_URL` in `.env`."
 * )
 *
 * @OA\Tag(name="Authentication", description="Register, login, password reset, token refresh, and social login.")
 * @OA\Tag(name="User", description="User details (`GET /api/users/{id}` public, `GET /api/users/me` and `GET /api/profile` authenticated), library, and account endpoints.")
 * @OA\Tag(name="Publications", description="Search, list, detail, favourites, comments, content requests, and home-style section feeds. Some GET routes use optional Passport token middleware for personalisation.")
 * @OA\Tag(name="Home", description="Aggregated home dashboard payload.")
 * @OA\Tag(name="Lookup", description="Reference data for forms and filters: themes, jobs, communities, categories, authors, settings.")
 * @OA\Tag(name="Forums", description="Forum threads, comments, likes, and participation.")
 * @OA\Tag(name="Communities", description="Communities of practice: browse, join, members, events, publications, and forums.")
 * @OA\Tag(name="Events", description="Events listing and authenticated creation.")
 * @OA\Tag(name="Experts", description="Expert directory and profiles.")
 * @OA\Tag(name="Members", description="Member states / geography helpers.")
 * @OA\Tag(name="Courses", description="Training courses catalogue.")
 * @OA\Tag(name="Health topics", description="Health topics and health emergencies listings.")
 * @OA\Tag(name="AI Operations", description="Summarisation and conversational assistant. Use `POST /api/ai/chat` for a single request/response flow, or `POST /api/ai/assistant/session` plus `POST /api/ai/assistant/message` for an explicit two-step flow. `POST /api/ai/summarise` is a one-shot summary.")
 * @OA\Tag(name="PushNotifications", description="Notification inbox, unread count, and mark-as-read for authenticated users.")
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
