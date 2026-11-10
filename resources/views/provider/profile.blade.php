@extends('layouts.provider')

@section('content')
    <h1>Profile and verification</h1>
    <div class="m">Customers see your photo, first name and vehicle. The verified badge is set only by MrRel.</div>

    <div class="sp" style="margin-top: 20px;">
        <!-- LEFT COLUMN -->
        <div>
            <div class="card">
                <h3>Photos</h3>
                <div class="row" style="margin-top: 12px; align-items: flex-start; gap: 20px;">
                    <div style="text-align: center;">
                        <div class="av" style="width: 120px; height: 120px; font-size: 40px; border-radius: 50%;">P</div>
                        <div class="sm" style="margin-top: 8px;"><b>Profile photo (required)</b></div>
                        <div class="m sm" style="margin-bottom: 8px;">A clear, recent photo of your face. No sunglasses, just you.</div>
                        <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">Change photo</button>
                    </div>
                    <div style="text-align: center;">
                        <div style="width: 160px; height: 120px; background-color: #E3F1F2; border: 1px solid var(--l); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <svg width="80" height="40" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="5" y="10" width="45" height="20" rx="2" fill="#D5DEE6"/>
                                <path d="M50 15H65L75 25V30H50V15Z" fill="#D5DEE6"/>
                                <circle cx="20" cy="32" r="5" fill="#51657A"/>
                                <circle cx="60" cy="32" r="5" fill="#51657A"/>
                            </svg>
                        </div>
                        <div class="sm" style="margin-top: 8px;"><b>Vehicle photo (required with a vehicle)</b></div>
                        <div class="m sm" style="margin-bottom: 8px;">The whole vehicle, number plate visible.</div>
                        <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">Change photo</button>
                    </div>
                    <div style="text-align: center;">
                        <div class="g2" style="gap: 4px; margin:0;">
                            <div style="width: 60px; height: 50px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 4px;"></div>
                            <div style="width: 60px; height: 50px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 4px;"></div>
                            <div style="width: 60px; height: 50px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 4px;"></div>
                            <div style="width: 60px; height: 50px; background: #E3F1F2; border: 1px solid #D5DEE6; border-radius: 4px;"></div>
                        </div>
                        <div class="sm" style="margin-top: 8px;"><b>Previous work (up to 10)</b></div>
                        <button type="button" class="btn g" style="padding: 4px 12px; font-size: 12px; margin:0;">Add photos</button>
                    </div>
                </div>
                <p class="m sm" style="margin-top: 16px;">A clear, recent photo of your face. JPG, PNG or WebP, up to 5 MB.</p>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Business details</h3>
                <div class="f">
                    <label>Business or trading name<input type="text" value="Sample Movers B"></label>
                    <label>Individual or business<select><option>Business</option></select></label>
                    <label>Business registration number<input type="text"></label>
                    <label>Vehicle<select><option>Bakkie</option></select></label>
                    <label>Number plate (shown to customers only after booking)<input type="text" value="CA 000 000"></label>
                    <label>Services (cleaning, packing, movers, storage, assembly, handyman, painting, gardening, plumbing, electrical)<input type="text"></label>
                    <label>Cities and areas you serve<input type="text"></label>
                    <label>Insurance<select><option>Yes</option></select></label>
                </div>
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; margin-top: 8px;">
                    <input type="checkbox" style="width: auto; margin: 0;" checked> I agree to MrRel showing my photo, first name and vehicle details
                </label>
                <div style="margin-top: 16px;">
                    <button type="button" class="btn">Save</button>
                    <button type="button" class="btn g">Preview public profile</button>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <div class="card">
                <h3>Verification status</h3>
                <table class="tb">
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>Photo matches ID</td>
                        <td><span class="chip ok">Verified</span></td>
                    </tr>
                    <tr>
                        <td>ID</td>
                        <td><span class="chip ok">Verified</span></td>
                    </tr>
                    <tr>
                        <td>Vehicle and plate</td>
                        <td><span class="chip ok">Verified</span></td>
                    </tr>
                    <tr>
                        <td>Insurance document</td>
                        <td><span class="chip wn">Pending</span></td>
                    </tr>
                    <tr>
                        <td>References</td>
                        <td><span class="chip ok">Verified</span></td>
                    </tr>
                </table>
                <p class="m sm" style="margin-top: 12px;">Changes to photo or vehicle may need re-approval.</p>
            </div>

            <div class="card" style="margin-top: 16px;">
                <h3>Documents</h3>
                <ul>
                    <li>ID document (private)</li>
                    <li>Insurance certificate (private)</li>
                </ul>
                <button type="button" class="btn g" style="margin-top: 12px;">Upload document</button>
            </div>
        </div>
    </div>
@endsection