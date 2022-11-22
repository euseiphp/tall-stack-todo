<?php

namespace App\Http\Livewire\ToDo;

use App\Models\Todo;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class Overview extends Component
{
    public string $title = '';

    public $todos;

    protected $listeners = [
        'overview::component::refresh' => '$refresh',
    ];

    public function render(): View
    {
        $this->todos = Auth::user()->todos()->get();

        return view('livewire.to-do.overview');
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('todos')
                    ->where(
                        fn ($query) => $query->where('user_id', Auth::id())
                    ),
            ],
        ];
    }

    public function create(): void
    {
        $this->validate();

        Auth::user()
            ->todos()
            ->create([
                'title' => $this->title,
            ]);

        $this->reset('title');

        $this->emitSelf('overview::component::refresh');
    }

    public function completed(Todo $todo): void
    {
        $todo->update([
            'is_completed' => ! $todo->is_completed,
        ]);

        $this->emitSelf('overview::component::refresh');
    }

    public function destroy(Todo $todo): void
    {
        $todo->delete();

        $this->emitSelf('overview::component::refresh');
    }
}
