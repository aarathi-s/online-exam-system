<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->title }} - Question {{ $questionIndex + 1 }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen" id="exam-body">

<div id="violation-banner" class="hidden bg-red-600 text-white text-center py-2 text-sm font-semibold"></div>

<div class="max-w-3xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-4 mb-4 flex justify-between items-center">
        <div>
            <h1 class="font-bold text-lg">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-500">Question {{ $questionIndex + 1 }} of {{ $questions->count() }}</p>
        </div>
        <div class="text-center">
            <div id="timer" class="text-2xl font-bold text-blue-600"></div>
            <div class="text-xs text-gray-400">remaining</div>
        </div>
    </div>

    <!-- Progress bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex gap-1 flex-wrap">
            @foreach($questions as $i => $q)
                @php
                    $isCurrent = $q->id == $question->id;
                    $isAnswered = $answeredIds->contains($q->id);
                @endphp
                <a href="{{ route('exam.question', ['sessionId' => $session->id, 'questionId' => $q->id]) }}"
                   class="w-8 h-8 rounded text-xs flex items-center justify-center font-medium
                   {{ $isCurrent ? 'bg-blue-600 text-white' : ($isAnswered ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300') }}">
                    {{ $i + 1 }}
                </a>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-2">
            <span class="inline-block w-3 h-3 bg-green-500 rounded mr-1"></span>Answered
            <span class="inline-block w-3 h-3 bg-blue-600 rounded mx-1 ml-3"></span>Current
            <span class="inline-block w-3 h-3 bg-gray-200 rounded mx-1 ml-3"></span>Unanswered
        </p>
    </div>

    <!-- Question -->
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="text-lg font-semibold mb-6">{{ $question->question_text }}</h2>

        <div class="space-y-3" id="options">
            @foreach($question->options as $option)
                <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-blue-50
                    {{ $currentAnswer && $currentAnswer->option_id == $option->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="option" value="{{ $option->id }}"
                           {{ $currentAnswer && $currentAnswer->option_id == $option->id ? 'checked' : '' }}
                           class="text-blue-600">
                    <span>{{ $option->option_text }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex justify-between">
        @if($questionIndex > 0)
            <a href="{{ route('exam.question', ['sessionId' => $session->id, 'questionId' => $questions[$questionIndex - 1]->id]) }}"
               class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">← Previous</a>
        @else
            <div></div>
        @endif

        @if($questionIndex < $questions->count() - 1)
            <a href="{{ route('exam.question', ['sessionId' => $session->id, 'questionId' => $questions[$questionIndex + 1]->id]) }}"
               class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Next →</a>
        @else
            <button onclick="confirmSubmit()"
                    class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                ✓ Submit Exam
            </button>
        @endif
    </div>
</div>

<!-- Submit form -->
<form id="submit-form" method="POST" action="{{ route('exam.submit', $session->id) }}">
    @csrf
</form>

<!-- Confirm submit modal -->
<div id="confirm-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full mx-4">
        <h3 class="text-lg font-bold mb-2">Submit Exam?</h3>
        <p class="text-gray-600 text-sm mb-4">
            You have answered
            <span class="font-semibold text-blue-600">{{ $answeredCount }}</span>
            out of
            <span class="font-semibold">{{ $questions->count() }}</span>
            questions. Are you sure you want to submit?
        </p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">Cancel</button>
            <button onclick="submitExam()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Yes, Submit</button>
        </div>
    </div>
</div>

<script>
// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Ensure modal is hidden on page load
    document.getElementById('confirm-modal').classList.add('hidden');
    
    startTimer();
    saveAnswerOnChange();
    setupViolationDetection();
});

// Timer function
function startTimer() {
    const remaining = Math.floor({{ $remaining }});
    let seconds = remaining;

    function updateTimer() {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        const timeStr = String(hours).padStart(2, '0') + ':' +
                       String(minutes).padStart(2, '0') + ':' +
                       String(secs).padStart(2, '0');
        
        document.getElementById('timer').textContent = timeStr;
        
        // Warning colors
        if (seconds <= 60) {
            document.getElementById('timer').classList.add('text-red-600');
            document.getElementById('timer').classList.remove('text-blue-600');
        }
        
        if (seconds <= 0) {
            document.getElementById('timer').textContent = '00:00:00';
            autoSubmitExam();
            return;
        }
        
        seconds--;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
}

// Save answer on radio change
function saveAnswerOnChange() {
    const optionsDiv = document.getElementById('options');
    if (!optionsDiv) return;
    
    const radios = optionsDiv.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const optionId = this.value;
            const sessionId = '{{ $session->id }}';
            const questionId = '{{ $question->id }}';
            
            // Show visual feedback that answer is being saved
            const parentLabel = this.closest('label');
            if (parentLabel) {
                parentLabel.style.opacity = '0.6';
            }
            
            fetch('{{ route("exam.answer", ["sessionId" => ":sessionId", "questionId" => ":questionId"]) }}'
                .replace(':sessionId', sessionId)
                .replace(':questionId', questionId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ option_id: optionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'saved') {
                    // Just update styling, don't reload page
                    if (parentLabel) {
                        parentLabel.style.opacity = '1';
                    }
                    showViolationBanner('Answer saved ✓');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (parentLabel) {
                    parentLabel.style.opacity = '1';
                }
            });
        });
    });
}

// Confirm submit
function confirmSubmit() {
    document.getElementById('confirm-modal').classList.remove('hidden');
}

// Close modal
function closeModal() {
    document.getElementById('confirm-modal').classList.add('hidden');
}

// Submit exam
function submitExam() {
    document.getElementById('submit-form').submit();
}

// Auto submit (time's up)
function autoSubmitExam() {
    showViolationBanner('Time is up! Your exam has been automatically submitted.');
    setTimeout(() => {
        document.getElementById('submit-form').submit();
    }, 2000);
}

// Violation detection
let lastViolationTime = {};
//let isPageUnloading = false;

function setupViolationDetection() {
    let hiddenTimeout;

    // Tab switch detection (no beforeunload interference)
    document.addEventListener('visibilitychange', function() {
        clearTimeout(hiddenTimeout);

        if (document.hidden) {
            hiddenTimeout = setTimeout(() => {
                if (!lastViolationTime['tab_switch'] || Date.now() - lastViolationTime['tab_switch'] > 5000) {
                    lastViolationTime['tab_switch'] = Date.now();
                    recordViolation('tab_switch');
                }
            }, 500);
        }
    });

    // Right click
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        if (!lastViolationTime['right_click'] || Date.now() - lastViolationTime['right_click'] > 2000) {
            lastViolationTime['right_click'] = Date.now();
            recordViolation('right_click');
        }
        return false;
    });

    // Copy
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        if (!lastViolationTime['copy_paste'] || Date.now() - lastViolationTime['copy_paste'] > 2000) {
            lastViolationTime['copy_paste'] = Date.now();
            recordViolation('copy_paste');
        }
    });

    // Paste
    document.addEventListener('paste', function(e) {
        e.preventDefault();
        if (!lastViolationTime['copy_paste'] || Date.now() - lastViolationTime['copy_paste'] > 2000) {
            lastViolationTime['copy_paste'] = Date.now();
            recordViolation('copy_paste');
        }
    });
}

// Record violation
function recordViolation(type) {
    const sessionId = '{{ $session->id }}';
    
    fetch('{{ route("exam.violation", ["sessionId" => ":sessionId"]) }}'
        .replace(':sessionId', sessionId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'terminated') {
            showViolationBanner(data.message);
            setTimeout(() => {
                document.getElementById('submit-form').submit();
            }, 2000);
        } else if (data.status === 'recorded') {
            showViolationBanner(`Violation recorded (${data.violations}/3). Your exam will be terminated after 3 violations.`);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Show violation banner
function showViolationBanner(message) {
    const banner = document.getElementById('violation-banner');
    banner.textContent = message;
    banner.classList.remove('hidden');
    setTimeout(() => {
        banner.classList.add('hidden');
    }, 5000);
}
</script>

</body>
</html>