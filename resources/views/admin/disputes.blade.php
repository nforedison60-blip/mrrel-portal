@extends('layouts.admin')

@section('content')
    <h1>Disputes</h1>
    <div class="m">Open, In progress and Resolved. A genuine dispute pauses the payout.</div>

    <div class="sp" style="margin-top: 20px;">
        
        <!-- LEFT: TICKETS LIST -->
        <div class="card">
            <h3>Tickets</h3>
            <table class="tb">
                <tr>
                    <th>Ticket</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>D-014 · BKG-0105</td>
                    <td><span class="chip wn">Open</span></td>
                </tr>
                <tr>
                    <td>D-013 · BKG-0099</td>
                    <td><span class="chip wn">In progress</span></td>
                </tr>
                <tr>
                    <td>D-012 · BKG-0090</td>
                    <td><span class="chip ok">Resolved</span></td>
                </tr>
            </table>
        </div>

        <!-- RIGHT: DETAILED VIEW -->
        <div class="card">
            <h3>D-014 · BKG-0105 · damaged item</h3>
            <div class="m sm" style="margin-bottom: 16px;">View chat · View GPS log · both are attached to the booking</div>

            <div class="g2" style="margin-bottom: 16px;">
                <!-- Customer Evidence -->
                <div class="card" style="background: #F5F8FA;">
                    <h3>Customer evidence</h3>
                    <div class="row" style="gap: 8px; margin-top: 12px;">
                        <div style="width: 60px; height: 60px; background: #E3F1F2; border-radius: 4px; border: 1px solid #D5DEE6;"></div>
                        <div style="width: 60px; height: 60px; background: #E3F1F2; border-radius: 4px; border: 1px solid #D5DEE6;"></div>
                        <div style="width: 60px; height: 60px; background: #E3F1F2; border-radius: 4px; border: 1px solid #D5DEE6;"></div>
                    </div>
                    <p class="m sm" style="margin-top: 12px;">Message and photo evidence.</p>
                </div>

                <!-- Provider Response -->
                <div class="card" style="background: #F5F8FA;">
                    <h3>Provider response</h3>
                    <div class="row" style="gap: 8px; margin-top: 12px;">
                        <div style="width: 60px; height: 60px; background: #E3F1F2; border-radius: 4px; border: 1px solid #D5DEE6;"></div>
                        <div style="width: 60px; height: 60px; background: #E3F1F2; border-radius: 4px; border: 1px solid #D5DEE6;"></div>
                        <div style="width: 60px; height: 60px; background: #E3F1F2; border-radius: 4px; border: 1px solid #D5DEE6;"></div>
                    </div>
                    <p class="m sm" style="margin-top: 12px;">Message and photo evidence.</p>
                </div>
            </div>

            <!-- Resolution Form -->
            <div class="f">
                <label>Outcome
                    <select>
                        <option>Redo</option>
                        <option>Refund</option>
                        <option>Dismissed</option>
                    </select>
                </label>
                <label>Resolution note
                    <input type="text" placeholder="e.g. Provider to redo the job">
                </label>
            </div>
            
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; margin-top: 8px;">
                <input type="checkbox" style="width: auto; margin: 0;"> Pause the payout
            </label>

            <div style="display: flex; gap: 8px; margin-top: 16px;">
                <button type="button" class="btn">Save</button>
                <button type="button" class="btn g">Resolve</button>
            </div>
        </div>
    </div>
@endsection