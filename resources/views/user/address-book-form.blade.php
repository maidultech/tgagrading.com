<div class="row address-block">
    <div class="form-group my-2 col-md-6">
        <label for="first_name">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" class="form-control"
               {{-- value="{{ old('first_name', $address->first_name ?? '') }}" required placeholder="Enter First Name"> --}}
               value="{{ old('first_name', auth()->user()->name ?? '') }}" readonly required placeholder="Enter First Name">
    </div>
    <div class="form-group my-2 col-md-6">
        <label for="last_name">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" class="form-control"
               value="{{ old('last_name', auth()->user()->last_name ?? '') }}"  readonly placeholder="Enter Last Name">
    </div>
    <div class="form-group my-2 col-md-6">
        <label for="country">Country <span class="text-danger">*</span></label>
        <select name="country" class="form-control form-select country" id="country">
            <option @selected($address?->country =='Canada') value="Canada">Canada</option>
            <option @selected($address?->country =='United States') value="United States">United States</option>
        </select>
    </div>

    <div class="form-group my-2 col-md-6">
        <label for="state">Province/Territory/State <span class="text-danger">*</span></label>
        @php
            $states = getStateCodeMap(ucwords($address?->country ?? 'Canada')) ?? '[]';
        @endphp
        <select name="state" class="form-control state" id="state" required>
            <option value="">Select State/Province</option>
            @foreach ($states as $key => $state)
                <option @selected($address?->state == ucwords($key)) value="{{ ucwords($key) }}">{{ ucwords($key) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group my-2 col-md-6">
        <label for="city">City <span class="text-danger">*</span></label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $address->city ?? '') }}" required placeholder="Enter City">
    </div>
    <div class="form-group my-2 col-md-6">
        <label for="street">Street <span class="text-danger">*</span></label>
        <input type="text" name="street" class="form-control" value="{{ old('street', $address->street ?? '') }}"
               required placeholder="Enter Street">
    </div>
    <div class="form-group my-2 col-md-6">
        <label for="apt_unit">Apt/Unit</label>
        <input type="text" name="apt_unit" class="form-control" value="{{ old('apt_unit', $address->apt_unit ?? '') }}"
            placeholder="Enter Apt/Unit">
    </div>
    <div class="form-group my-2 col-md-6">
        <label for="zip_code">Zip Code <span class="text-danger">*</span></label>
        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $address->zip_code ?? '') }}"
               required placeholder="Enter Zip Code">
    </div>
</div>
