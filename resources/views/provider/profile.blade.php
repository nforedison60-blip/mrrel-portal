@extends('layouts.provider')

@section('content')
    <h1>Profile and Verification</h1>
    <div class="m" style="margin-bottom: 20px;">Update your details and upload your documents to get verified.</div>

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
                            @if($provider->image_path)
                                <img src="{{ Storage::disk('s3')->url($provider->image_path) }}" alt="Your profile photo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto;">
                            @else
                                <div class="av" style="width: 120px; height: 120px; font-size: 48px; border-radius: 50%; margin: 0 auto; background: var(--a); color: var(--n); display: flex; align-items: center; justify-content: center;">
                                    {{ substr($provider->name, 0, 1) }}
                                </div>
                            @endif
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
                            @if($provider->vehicle_image_path)
                                <img src="{{ Storage::disk('s3')->url($provider->vehicle_image_path) }}" alt="Your vehicle photo" style="width: 160px; height: 120px; border-radius: 8px; object-fit: cover; display: block; margin: 0 auto;">
                            @else
                            <div style="width: 160px; height: 120px; background-color: #E3F1F2; border: 1px solid var(--l); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <svg width="100" height="50" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                                    <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                                    <circle cx="20" cy="32" r="5" fill="#51657A"/>
                                    <circle cx="60" cy="32" r="5" fill="#51657A"/>
                                </svg>
                            </div>
                            @endif
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
                    <h3>Service area</h3>
                    <div class="m sm">You only see and quote on moves that start near you. Customers only deal with movers close to them.</div>
                    <input type="hidden" name="base_lat" id="baseLat">
                    <input type="hidden" name="base_lng" id="baseLng">
                    <div class="f">
                        <label class="k">Where are you based? (suburb and city)
                            <input type="text" name="base_label" id="baseLabel" value="{{ old('base_label', $provider->base_label) }}" maxlength="150" placeholder="e.g. Sandton, Johannesburg">
                        </label>
                        <label>How far will you travel?
                            <select name="service_radius_km">
                                @foreach([10 => '10 km', 25 => '25 km', 50 => '50 km', 100 => '100 km', 200 => '200 km'] as $km => $text)
                                    <option value="{{ $km }}" {{ (int) old('service_radius_km', $provider->service_radius_km ?: 50) === $km ? 'selected' : '' }}>{{ $text }} from my base</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <div style="margin-top: 8px;">
                        <button type="button" class="btn g" id="useBaseLocation" style="padding: 6px 14px; font-size: 13px;">Use my current location</button>
                        <span class="m sm" id="baseNote">Or type your suburb above.</span>
                    </div>
                    @if($provider->base_lat !== null)
                        <div class="chip ok" style="margin-top: 8px;">Area saved: {{ $provider->base_label }} · {{ $provider->service_radius_km }} km</div>
                    @else
                        <div class="chip wn" style="margin-top: 8px;">Set your area to see requests near you</div>
                    @endif
                    <div class="m sm" style="margin-top: 6px;">Address search by OpenStreetMap.</div>
                    <script>
                        (function () {
                            var btn = document.getElementById('useBaseLocation'), note = document.getElementById('baseNote');
                            var lat = document.getElementById('baseLat'), lng = document.getElementById('baseLng'), label = document.getElementById('baseLabel');
                            btn.addEventListener('click', function () {
                                if (!navigator.geolocation) { note.textContent = 'Your browser cannot share its location. Type your suburb instead.'; return; }
                                note.textContent = 'Finding your location...';
                                navigator.geolocation.getCurrentPosition(function (pos) {
                                    lat.value = pos.coords.latitude.toFixed(7); lng.value = pos.coords.longitude.toFixed(7);
                                    note.textContent = 'Location captured. Press Save to keep it.';
                                }, function () {
                                    note.textContent = 'We could not get your location. Please type your suburb instead.';
                                }, { enableHighAccuracy: true, timeout: 10000 });
                            });
                            // Typing a different place cancels the captured location
                            label.addEventListener('input', function () { lat.value = ''; lng.value = ''; });
                        })();
                    </script>
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Payout details</h3>
                    <div class="m sm">Where MrRel pays you for your jobs. Only you and the MrRel team can see these. The account must be in your own name or your business name.</div>
                    <div class="f">
                        <label>Bank
                            <select name="bank_name">
                                <option value="">Choose your bank</option>
                                @foreach(['Absa','Capitec','FNB','Nedbank','Standard Bank','African Bank','Discovery Bank','TymeBank','Investec','Other'] as $bank)
                                    <option value="{{ $bank }}" {{ old('bank_name', $provider->bank_name) === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Account type
                            <select name="bank_account_type">
                                <option value="">Choose</option>
                                @foreach(['Cheque / Current','Savings','Transmission'] as $type)
                                    <option value="{{ $type }}" {{ old('bank_account_type', $provider->bank_account_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="k">Account holder name
                            <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $provider->bank_account_holder) }}" maxlength="150" placeholder="Name on the bank account">
                        </label>
                        <label>Account number
                            <input type="text" name="bank_account_number" inputmode="numeric" autocomplete="off" maxlength="16"
                                   placeholder="{{ $provider->bank_account_number ? '•••• ' . substr($provider->bank_account_number, -4) . ' (leave blank to keep)' : 'Digits only' }}">
                        </label>
                        <label>Branch code
                            <input type="text" name="bank_branch_code" inputmode="numeric" maxlength="6" value="{{ old('bank_branch_code', $provider->bank_branch_code) }}" placeholder="6 digits">
                        </label>
                    </div>
                    @if($provider->hasBankDetails())
                        <div class="chip ok" style="margin-top: 8px;">Payout details saved</div>
                    @else
                        <div class="chip wn" style="margin-top: 8px;">Add these so MrRel can pay you</div>
                    @endif
                </div>

                <div class="card" style="margin-top: 16px;">
                    <h3>Documents</h3>
                    <div class="f" style="margin-top: 16px;">
                        <label class="k">ID Document (PDF/Image)
                            <input type="file" name="id_document" accept=".pdf,image/*">
                            @if($provider->id_document)
                                <div class="m sm" style="margin-top: 4px;"><a href="{{ route('documents.show', [$provider->id, 'id_document']) }}" target="_blank">View Uploaded ID</a></div>
                            @endif
                        </label>
                        <label class="k">Vehicle Document (PDF/Image)
                            <input type="file" name="vehicle_document" accept=".pdf,image/*">
                            @if($provider->vehicle_document)
                                <div class="m sm" style="margin-top: 4px;"><a href="{{ route('documents.show', [$provider->id, 'vehicle_document']) }}" target="_blank">View Uploaded Vehicle Docs</a></div>
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
                        @php
                            $isVerified = $provider->verification_status === 'Verified';
                            $hasId = $provider->id_document || $provider->id_image_path;
                            $checks = [
                                'Photo matches ID' => $provider->image_path && $hasId,
                                'ID' => $hasId,
                                'Vehicle and plate' => $provider->vehicle_image_path && $provider->plate_number,
                            ];
                        @endphp
                        @foreach($checks as $label => $uploaded)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>
                                    @if(!$uploaded)
                                        <span class="chip wn">Missing</span>
                                    @elseif($isVerified)
                                        <span class="chip ok">Verified</span>
                                    @else
                                        <span class="chip wn">Under review</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr><td>Insurance document</td><td><span class="chip {{ $isVerified ? 'ok' : 'wn' }}">{{ $isVerified ? 'Verified' : 'Pending' }}</span></td></tr>
                        <tr><td>References</td><td><span class="chip {{ $isVerified ? 'ok' : 'wn' }}">{{ $isVerified ? 'Verified' : 'Pending' }}</span></td></tr>
                    </table>
                    <div class="m sm" style="margin-top: 12px;">Changes to photo or vehicle may need re-approval.</div>
                </div>
            </div>
        </div>
    </form>
@endsection