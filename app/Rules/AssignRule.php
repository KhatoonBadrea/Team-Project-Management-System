<?php

namespace App\Rules;

use Closure;
use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;

class AssignRule implements ValidationRule
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $project = Project::find($this->projectId);

        if (!$project) {
            $fail('The project does not exist.');
            return;
        }

        $userInProject = $project->users()
            ->where('user_id', $value)
            ->whereIn('role', ['developer', 'tester']) 
            ->exists();

        if (!$userInProject) {
            $fail('The selected user is not a developer or tester in the team of this project.');
        }
    }
}
