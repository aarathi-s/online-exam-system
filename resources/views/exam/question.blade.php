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

<div id="violation-banner" class="hidden fixed top-0 left-0 right-0 z-50 bg-red-600 text-white text-center py-3 text-sm font-semibold shadow-lg">
    <span id="violation-message"></span>
</div>

{{-- Save toast (was missing from original) --}}
<div id="save-toast" class="hidden fixed bottom-4 right-4 z-50 bg-green-600 text-white text-sm px-4 py-2 rounded shadow-lg">
    ✓ Answer saved
</div>

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
            {{-- Fix: use ->get() instead of [] for PostgreSQL compatibility --}}
            <a href="{{ route('exam.question', ['sessionId' => $session->id, 'questionId' => $questions->get($questionIndex - 1)->id]) }}"
               class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">← Previous</a>
        @else
            <div></div>
        @endif

        @if($questionIndex < $questions->count() - 1)
            <a href="{{ route('exam.question', ['sessionId' => $session->id, 'questionId' => $questions->get($questionIndex + 1)->id]) }}"
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
let violationCount = 0;
const MAX_VIOLATIONS = 3;
let lastViolationTime = {};
let saveToastTimer = null;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirm-modal').classList.add('hidden');
    startTimer();
    saveAnswerOnChange();
    setupViolationDetection();
});

function startTimer() {
    let seconds = Math.floor({{ $remaining }});

    function updateTimer() {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        const timerEl = document.getElementById('timer');
        timerEl.textContent =
            String(h).padStart(2,'0') + ':' +
            String(m).padStart(2,'0') + ':' +
            String(s).padStart(2,'0');

        if (seconds <= 60) {
            timerEl.classList.add('text-red-600');
            timerEl.classList.remove('text-blue-600');
        }
        if (seconds <= 0) {
            timerEl.textContent = '00:00:00';
            autoSubmitExam();
            return;
        }
        seconds--;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
}

function saveAnswerOnChange() {
    const radios = document.querySelectorAll('#options input[type="radio"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            const optionId = this.value;
            const formData = new FormData();
            formData.append('option_id', optionId);

            fetch('{{ route("exam.answer", ["sessionId" => ":sid", "questionId" => ":qid"]) }}'
                .replace(':sid', '{{ $session->id }}')
                .replace(':qid', '{{ $question->id }}'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                showSaveToast();
            })
            .catch(err => console.error(err));
        });
    });
}

function showSaveToast() {
    const toast = document.getElementById('save-toast');
    toast.classList.remove('hidden');
    clearTimeout(saveToastTimer);
    saveToastTimer = setTimeout(() => toast.classList.add('hidden'), 2000);
}

function showViolationBanner(message, autoHide = false) {
    const banner = document.getElementById('violation-banner');
    const msg    = document.getElementById('violation-message');
    msg.textContent = message;
    banner.classList.remove('hidden');
    if (autoHide) {
        setTimeout(() => banner.classList.add('hidden'), 5000);
    }
}

function confirmSubmit() {
    document.getElementById('confirm-modal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('confirm-modal').classList.add('hidden');
}
function submitExam() {
    document.getElementById('submit-form').submit();
}
function autoSubmitExam() {
    showViolationBanner('⏰ Time is up! Submitting your exam...');
    setTimeout(() => document.getElementById('submit-form').submit(), 2000);
}

function setupViolationDetection() {
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            setTimeout(() => {
                if (!lastViolationTime['tab_switch'] ||
                    Date.now() - lastViolationTime['tab_switch'] > 5000) {
                    lastViolationTime['tab_switch'] = Date.now();
                    recordViolation('tab_switch');
                }
            }, 500);
        }
    });

    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        if (!lastViolationTime['right_click'] ||
            Date.now() - lastViolationTime['right_click'] > 2000) {
            lastViolationTime['right_click'] = Date.now();
            recordViolation('right_click');
        }
    });

    ['copy','paste'].forEach(evt => {
        document.addEventListener(evt, function(e) {
            e.preventDefault();
            if (!lastViolationTime['copy_paste'] ||
                Date.now() - lastViolationTime['copy_paste'] > 2000) {
                lastViolationTime['copy_paste'] = Date.now();
                recordViolation('copy_paste');
            }
        });
    });
}

// ── FIXED: single complete fetch chain ────────────────────
function recordViolation(type) {
    const formData = new FormData();
    formData.append('type', type);

    fetch('{{ route("exam.violation", ["sessionId" => ":sid"]) }}'
        .replace(':sid', '{{ $session->id }}'), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'terminated') {
            document.body.innerHTML = `
                <div style="position:fixed;inset:0;background:#dc2626;color:white;
                    display:flex;flex-direction:column;align-items:center;
                    justify-content:center;z-index:9999;font-family:sans-serif;">
                    <div style="font-size:4rem;">🚫</div>
                    <h1 style="font-size:2rem;font-weight:bold;margin:1rem 0;">Exam Terminated</h1>
                    <p style="font-size:1.1rem;opacity:0.9;">Too many violations detected. Your exam has been submitted.</p>
                    <p style="margin-top:0.5rem;opacity:0.7;">Redirecting...</p>
                </div>`;
            setTimeout(() => document.getElementById('submit-form')?.submit(), 2500);
        } else if (data.status === 'recorded') {
            violationCount = data.violations;
            const remaining = MAX_VIOLATIONS - violationCount;

            const typeLabel = {
                'tab_switch' : '⚠️ Tab switch detected!',
                'right_click': '⚠️ Right-click detected!',
                'copy_paste' : '⚠️ Copy/Paste detected!'
            }[type] || '⚠️ Violation detected!';

            showViolationBanner(
                `${typeLabel} — Violation ${violationCount}/${MAX_VIOLATIONS}. ` +
                (remaining > 0
                    ? `${remaining} more will auto-submit your exam.`
                    : `Submitting now...`)
            );

            if (violationCount >= MAX_VIOLATIONS) {
                setTimeout(() => document.getElementById('submit-form').submit(), 2500);
            }
        }
    })
    .catch(console.error);
}
</script>

</body>
</html>