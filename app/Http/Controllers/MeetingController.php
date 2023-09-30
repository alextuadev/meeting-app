<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleMeetingRequest;
use App\Models\Meeting;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;


class MeetingController extends Controller
{

    protected function scheduleMeeting(string $meetingName, DateTime $startDateTime, DateTime $endDateTime, array $userIDs): array
    {
        // Iterate through each user to check for conflicts
        foreach ($userIDs as $userID) {
            // Look for meetings that have a time conflict for this specific user
            $conflictingMeeting = Meeting::query()->where('user_id', $userID)
                // Meeting starts before the new one ends and ends after the new one starts
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->where(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where('start_time', '<', $endDateTime)
                            ->where('end_time', '>', $startDateTime);
                    })
                        // Or meeting starts or ends exactly at the same time
                        ->orWhere('start_time', $startDateTime)
                        ->orWhere('end_time', $endDateTime);
                })
                ->first();

            if ($conflictingMeeting) {
                $message = "User {$userID} has a conflicting meeting: {$conflictingMeeting->meeting_name},  ";
                $message .= "The meeting has not been booked.";
                return ['success' => false, 'message' => $message];
            }
        }

        // If no conflicts, proceed to schedule the meeting for all users
        foreach ($userIDs as $userId) {
            Meeting::query()->create([
                'user_id' => $userId,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'meeting_name' => $meetingName,
            ]);
        }

        return [
            'success' => true,
            'message' => "The meeting has been successfully booked"
        ];
    }

    /**
     * @throws Exception
     */
    public function schedule(ScheduleMeetingRequest $request): JsonResponse
    {
        // Parse userIDs from comma-separated string to an array
        $userIDs = array_map('intval', explode(',', $request->input('userIDs')));

        // Convert string dates to DateTime objects
        $startDateTime = new DateTime($request->input('startDateTime'));
        $endDateTime = new DateTime($request->input('endDateTime'));

        // Call the scheduleMeeting function
        $result = $this->scheduleMeeting(
            $request->input('meetingName'),
            $startDateTime,
            $endDateTime,
            $userIDs
        );

        if ($result['success']) {
            return response()->json(['message' => 'The meeting has been successfully booked.'], 200);
        } else {
            return response()->json(['message' => $result['message']], 400);
        }
    }

}

