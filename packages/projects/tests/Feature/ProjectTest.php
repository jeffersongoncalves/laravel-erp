<?php

use JeffersonGoncalves\Erp\Projects\Enums\ProjectStatus;
use JeffersonGoncalves\Erp\Projects\Enums\TaskPriority;
use JeffersonGoncalves\Erp\Projects\Enums\TaskStatus;
use JeffersonGoncalves\Erp\Projects\Models\Project;
use JeffersonGoncalves\Erp\Projects\Models\Task;

it('creates a project with default attributes', function () {
    $project = Project::factory()->create();

    expect($project->status)->toBe(ProjectStatus::Open)
        ->and($project->party_type)->toBe('Customer')
        ->and($project->percent_complete)->toBe(0.0)
        ->and($project->total_billable_amount)->toBe(0.0);
});

it('casts the project status to its enum', function () {
    $project = Project::factory()->create(['status' => 'Completed']);

    expect($project->status)->toBeInstanceOf(ProjectStatus::class)
        ->and($project->status)->toBe(ProjectStatus::Completed);
});

it('creates a task with default status and priority', function () {
    $task = Task::factory()->create();

    expect($task->status)->toBe(TaskStatus::Open)
        ->and($task->priority)->toBe(TaskPriority::Medium)
        ->and($task->is_group)->toBeFalse();
});

it('relates tasks to a project', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($task->project->id)->toBe($project->id)
        ->and($project->tasks->pluck('id'))->toContain($task->id);
});

it('builds a parent task tree', function () {
    $parent = Task::factory()->group()->create();
    $child = Task::factory()->create(['parent_task_id' => $parent->id]);

    expect($parent->is_group)->toBeTrue()
        ->and($child->parentTask->id)->toBe($parent->id)
        ->and($parent->childTasks->pluck('id'))->toContain($child->id);
});
