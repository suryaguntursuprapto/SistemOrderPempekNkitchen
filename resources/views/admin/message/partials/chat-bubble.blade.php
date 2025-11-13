@foreach($messages as $msg)
    @if($msg->message !== '[SYSTEM_INIT]')
        <div class="flex justify-start mb-4 gap-3">
            <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center text-xs font-bold text-gray-600">
                {{ substr($msg->user->name, 0, 1) }}
            </div>
            <div class="flex flex-col items-start max-w-[75%]">
                <div class="bg-gray-100 px-4 py-2.5 rounded-2xl rounded-tl-none text-sm text-gray-800 leading-relaxed">
                    {{ $msg->message }}
                </div>
                <span class="text-[10px] text-gray-400 mt-1 ml-1">
                    {{ $msg->created_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB
                </span>
            </div>
        </div>
    @endif

    @if($msg->admin_reply)
    <div class="flex justify-end mb-4 gap-3">
        <div class="flex flex-col items-end max-w-[75%]">
            <div class="bg-orange-500 px-4 py-2.5 rounded-2xl rounded-tr-none text-sm text-white leading-relaxed shadow-sm">
                {{ $msg->admin_reply }}
            </div>
            <div class="flex items-center gap-1 mt-1 mr-1">
                <span class="text-[10px] text-gray-400">
                    {{ $msg->replied_at ? \Carbon\Carbon::parse($msg->replied_at)->setTimezone('Asia/Jakarta')->format('H:i') : '' }} WIB
                </span>
                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7m-5-5l4 4m-4-4L9 17"></path>
                </svg>
            </div>
        </div>
    </div>
    @endif
@endforeach