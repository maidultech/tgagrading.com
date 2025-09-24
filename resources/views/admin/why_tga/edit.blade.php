<form action="{{ route('admin.why-tga.update', $why_tga->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" id="title" value="{{ $why_tga->title }}" class="form-control"
            placeholder="Category name" required>
    </div>
    <div class="form-group">
        <label for="order_id" class="form-label">Order Number</label>
        <input type="number" name="order_id" id="order_id" value="{{ $why_tga->order_id }}" class="form-control"
            placeholder="Order Number" required>
    </div>
    <div class="form-group">
        <label for="destails" class="form-label">Details</label>
        <textarea name="destails" id="destails" class="form-control" placeholder="Details" required>{{ $why_tga->destails }}</textarea>
    </div>
    <div class="form-group">
        <label for="status" class="form-label">Published Status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" {{ $why_tga->status == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $why_tga->status == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <div class="form-group float-right">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
