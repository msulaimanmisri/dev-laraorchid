<?php

namespace App\Orchid\Screens;

use App\Models\Task;
use Orchid\Screen\TD;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;

class TaskScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'tasks' => Task::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Task Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'All of your tasks listed here';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Task')
                ->modal('taskModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            // Table
            Layout::table('tasks', [
                TD::make('name'),
                TD::make('created_at')->sort(),
                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Task $task) {
                        return Button::make('Delete Task')
                            ->confirm('After deleting, the task will be gone forever.')
                            ->method('delete', ['task' => $task->id]);
                    }),
            ]),

            // Modal
            Layout::modal('taskModal', Layout::rows([
                Input::make('name')
                    ->title('Name')
                    ->placeholder('Enter task name')
                    ->help('The name of the task to be created.'),
            ]))
                ->title('Create Task')
                ->applyButton('Add Task'),
        ];
    }

    /**
     * Create method
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255']
        ]);

        Task::create(['name' => $request->name]);
    }

    /**
     * Delete Method
     */
    public function delete(Task $task)
    {
        $task->delete();
    }
}
