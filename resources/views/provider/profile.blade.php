@extends('layouts.provider')

@section('content')
    <h1>Profile and Verification</h1>
    <div class="m" style="margin-bottom: 20px;">Update your details and upload your documents to get verified.</div>

    @if(session('success'))
        <div style="background:#E3F1F2; color:#0A5C64; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #0E7C86;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#FDF1CC; color:#5C4300; padding:12px 18px; border-radius:12px; margin-bottom:16px; font-weight:600; border:1px solid #F4B731;">
            Please check the form for errors.
        </div>
    @endif

    <form method="POST" action="{{ route('provider.profile.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="sp">
            <!-- LEFT COLUMN (Form Fields) -->
            <div>
                <div class="card">
                    <h3>Photos</h3>
                    <div class="row" style="margin-top: 16px; gap: 20px; align-items: flex-start;">
                        <div style="text-align: center; flex: 1;">
                            <div class="av" style="width: 120px; height: 120px; font-size: 48px; border-radius: 50%; margin: 0 auto; background: var(--a); color: var(--n); display: flex; align-items: center; justify-content: center;">
                                {{ substr($provider->name, 0, 1) }}
                            </div>
                            <div class="m sm" style="margin-top: 8px;"><b>Profile photo</b><br>Clear photo of your face.</div>
                            <label class="btn g" style="margin-top: 8px; cursor: pointer; display: inline-block;">
                                Change photo
                                <input type="file" name="image" accept="image/*" style="display: none;">
                            </label>
                            @if($provider->image_path)
                                <div class="m sm" style="margin-top: 4px; color: #10b981;">Photo uploaded ✓</div>
                            @endif
                        </div>
                        <div style="text-align: center; flex: 1;">
                            <div style="width: 160px; height: 120px; background-color: #E3F1F2; border: 1px solid var(--l); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <svg width="100" height="50" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                                    <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                                    <circle cx="20" cy="32" r="5" fill="#51657A"/>
                                    <circle cx="60" cy="32" r="5" fill="#51657A"/>
                                </svg>
                            </div>
                            <div class="m sm" style="margin-top: 8px;"><b>Vehicle photo</b><br>Number plate visible.</div>
                            <label class="btn g" style="margin-top: 8px; cursor: pointer; display: inline-block;">
                                Change photo
                                <input type="file" name="vehicle_image" accept="image/*" style="display: none;">
                            </label>
                            @if($provider->vehicle_image_path)
                                <div class="m sm" style="margin-top: 4px; color: #10b981;">Vehicle uploaded ✓</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Business details</h3>
                    <div class="f" style="margin-top: 16px;">
                        <label class="k">Full Name
                            <input type="text" name="name" value="{{ $provider->name }}" required>
                        </label>
                        <label class="k">Business or trading name
                            <input type="text" name="company_name" value="{{ $provider->company_name }}">
                        </label>
                        <label class="k">Vehicle
                            <input type="text" name="vehicle" value="{{ $provider->vehicle }}">
                        </label>
                        <label class="k">Plate number
                            <input type="text" name="plate_number" value="{{ $provider->plate_number }}">
                        </label>
                    </div>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Documents</h3>
                    <div class="f" style="margin-top: 16px;">
                        <label class="k">ID Document (PDF/Image)
                            <input type="file" name="id_document" accept=".pdf,image/*">
                            @if($provider->id_document)
                                <div class="m sm" style="margin-top: 4px;"><a href="{{ Storage::disk('s3')->url($provider->id_document) }}" target="_blank">View Uploaded ID</a></div>
                            @endif
                        </label>
                        <label class="k">Vehicle Document (PDF/Image)
                            <input type="file" name="vehicle_document" accept=".pdf,image/*">
                            @if($provider->vehicle_document)
                                <div class="m sm" style="margin-top: 4px;"><a href="{{ Storage::disk('s3')->url($provider->vehicle_document) }}" target="_blank">View Uploaded Vehicle Docs</a></div>
                            @endif
                        </label>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%; font-size: 16px; padding: 15px;">Save Profile & Upload</button>
                </div>
            </div>

            <!-- RIGHT COLUMN (Status) -->
            <div>
                <div class="card st">
                    <h3>Verification status</h3>
                    <table class="tb" style="margin-top: 12px;">
                        <tr><th>Check</th><th>Status</th></tr>
                        <tr><td>Photo matches ID</td><td><span class="chip ok">Verified</span></td></tr>
                        <tr><td>ID</td><td><span class="chip ok">Verified</span></td></tr>
                        <tr><td>Vehicle and plate</td><td><span class="chip ok">Verified</span></td></tr>
                        <tr><td>Insurance document</td><td><span class="chip wn">Pending</span></td></tr>
                        <tr><td>References</td><td><span class="chip ok">Verified</span></td></tr>
                    </table>
                    <div class="m sm" style="margin-top: 12px;">Changes to photo or vehicle may need re-approval.</div>
                </div>
            </div>
        </div>
    </form>
@endsection