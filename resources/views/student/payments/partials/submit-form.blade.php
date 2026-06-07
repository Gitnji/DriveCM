<form method="POST" action="{{ route('student.payments.submit') }}" enctype="multipart/form-data" class="mt-4 border-t border-neutral/10 pt-4 space-y-3">
    @csrf
    <input type="hidden" name="payment_type_id" value="{{ $type->id }}">

    <div>
        <label class="block text-sm font-medium text-neutral" for="screenshot-{{ $type->id }}">Screenshot of payment</label>
        <input type="file" id="screenshot-{{ $type->id }}" name="screenshot" required
               accept="image/jpeg,image/png,image/webp"
               class="mt-1 block w-full text-sm text-neutral file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark">
        <p class="mt-1 text-xs text-neutral/50">JPEG, PNG, or WebP. Max 2 MB.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral" for="notes-{{ $type->id }}">Notes <span class="text-neutral/40">(optional)</span></label>
        <textarea id="notes-{{ $type->id }}" name="notes" rows="2" maxlength="1000"
                  class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                  placeholder="Optional message to your school."></textarea>
    </div>

    <button type="submit"
            class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
        Submit proof of payment
    </button>
</form>