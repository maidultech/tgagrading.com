<form action="{{ route('admin.state.update', $state->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="country_id" class="form-label font-weight-bold">Country</label>
        <select name="country_id" id="country_id" class="form-control" required>
            @foreach(\App\Models\Country::all() as $c)
                <option value="{{ $c->id }}" {{ $state->country_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="name" class="form-label">State Name</label>
        <input type="text" name="name" id="name" value="{{ $state->name }}" class="form-control"
            placeholder="State name" required>
    </div>
    <div class="form-group">
        <label for="gst" class="form-label">GST Rate (%)</label>
        <input type="number" step="0.01" name="gst" id="gst" value="{{ old('gst', $state->gst ?? 0) }}"
            class="form-control" placeholder="e.g., 5.0, 7.5" min="0" max="100">
    </div>

    <div class="form-group">
        <label for="pst" class="form-label">PST Rate (%)</label>
        <input type="number" step="0.01" name="pst" id="pst" value="{{ old('pst', $state->pst ?? 0) }}"
            class="form-control" placeholder="e.g., 1.0, 3.5" min="0" max="100">
    </div>

    <div class="form-group">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" {{ $state->status == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $state->status == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="form-group float-right">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>