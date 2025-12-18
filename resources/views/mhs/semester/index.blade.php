<x-main-layout>
    <form action="{{ route('student.semester.store') }}" method="POST">
        @csrf

    </form>
</x-main-layout>