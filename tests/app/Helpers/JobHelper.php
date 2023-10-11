<?php

namespace DTApi\Helpers;

use Carbon\Carbon;
use DTApi\Models\Job;
use DTApi\Models\User;
use DTApi\Models\Language;
use DTApi\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobHelper
{
    public static function getCustomerJobs($user)
    {
        return $user->jobs()
            ->with('user.userMeta', 'user.average', 'translatorJobRel.user.average', 'language', 'feedback')
            ->whereIn('status', ['pending', 'assigned', 'started'])
            ->orderBy('due', 'asc')
            ->get();
    }

    public static function getTranslatorJobs($user)
    {
        $jobs = Job::getTranslatorJobs($user->id, 'new');
        return $jobs->pluck('jobs')->all();
    }

    public static function separateJobs($jobitem, &$emergencyJobs, &$normalJobs, $user_id)
    {
        if ($jobitem->immediate == 'yes') {
            $emergencyJobs[] = $jobitem;
        } else {
            $normalJobs[] = $jobitem;
        }
    }


    public static function failResponse($fieldName, $message)
    {
        return [
            'status' => 'fail',
            'message' => $message,
            'field_name' => $fieldName,
        ];
    }

    public static function mapGender($jobFor)
    {
        if (in_array('male', $jobFor)) {
            return 'male';
        } elseif (in_array('female', $jobFor)) {
            return 'female';
        }

        return null;
    }

    public static function mapCertified($jobFor)
    {
        if (in_array('normal', $jobFor) && in_array('certified', $jobFor)) {
            return 'both';
        } elseif (in_array('normal', $jobFor)) {
            if (in_array('certified_in_law', $jobFor)) {
                return 'n_law';
            } elseif (in_array('certified_in_helth', $jobFor)) {
                return 'n_health';
            } else {
                return 'normal';
            }
        } elseif (in_array('certified', $jobFor)) {
            return 'certified';
        }

        return null;
    }

    public static function mapJobType($consumerType)
    {
        if ($consumerType == 'rwsconsumer') {
            return 'rws';
        } elseif ($consumerType == 'ngo') {
            return 'unpaid';
        } elseif ($consumerType == 'paid') {
            return 'paid';
        }

        return null;
    }

    public static function mapJobFor($job)
    {
        $jobFor = [];

        if ($job->gender != null) {
            if ($job->gender == 'male') {
                $jobFor[] = 'Man';
            } elseif ($job->gender == 'female') {
                $jobFor[] = 'Kvinna';
            }
        }

        if ($job->certified != null) {
            if ($job->certified == 'both') {
                $jobFor[] = 'normal';
                $jobFor[] = 'certified';
            } elseif ($job->certified == 'yes') {
                $jobFor[] = 'certified';
            } else {
                $jobFor[] = $job->certified;
            }
        }

        return $jobFor;
    }





}
