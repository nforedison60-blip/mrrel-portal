@extends('layouts.admin')

@section('content')
    <h1>Settings and reports</h1>
    <div class="m">Values the website and portal read from here.</div>

    <div class="g2" style="margin-top: 20px;">
        <!-- Fees -->
        <div class="card">
            <h3>Fees</h3>
            <div class="f">
                <label>Rental service fee (flat or banded) [R]<input type="text"></label>
                <label>Viewing fee: video [R]<input type="text"></label>
                <label>Viewing fee: in person [R]<input type="text"></label>
            </div>
        </div>

        <!-- Subscription Plans -->
        <div class="card">
            <h3>Subscription plans</h3>
            <div class="f">
                <label>Basic [R]<input type="text"></label>
                <label>Pro [R]<input type="text"></label>
                <label>Premium [R]<input type="text"></label>
                <label>Founding free period (months)<input type="text"></label>
            </div>
        </div>
    </div>

    <div class="g2" style="margin-top: 20px;">
        <!-- Rules -->
        <div class="card">
            <h3>Rules</h3>
            <div class="f">
                <label>Job counts as complete after (hours)<input type="text"></label>
                <label>Quote validity options
                    <select><option>24</option></select>
                </label>
                <label>Cancellation text<input type="text"></label>
                <label>Deposit or full payment rule<input type="text"></label>
            </div>
        </div>

        <!-- Site and Email -->
        <div class="card">
            <h3>Site and email</h3>
            <div class="f">
                <label>Phone, WhatsApp, email<input type="text"></label>
                <label>Address and registration number<input type="text"></label>
                <label class="k">Email template texts<input type="text"></label>
            </div>
        </div>
    </div>

    <!-- Reports -->
    <div class="card" style="margin-top: 20px;">
        <h3>Reports</h3>
        
        <div class="g3" style="margin-top: 16px;">
            <div class="card" style="background: #F5F8FA;">
                <div class="m sm">Tenants per week</div>
                <div class="big">9</div>
            </div>
            <div class="card" style="background: #F5F8FA;">
                <div class="m sm">Time to first options</div>
                <div class="big">1.5 days</div>
            </div>
            <div class="card" style="background: #F5F8FA;">
                <div class="m sm">Requests per provider</div>
                <div class="big">12 a month</div>
            </div>
            <div class="card" style="background: #F5F8FA;">
                <div class="m sm">Provider renewals</div>
                <div class="big">8 of 10</div>
            </div>
            <div class="card" style="background: #F5F8FA;">
                <div class="m sm">Refund and complaint rate</div>
                <div class="big">3%</div>
            </div>
            <div class="card" style="background: #F5F8FA;">
                <div class="m sm">Tenants who also book a move</div>
                <div class="big">4 of 9</div>
            </div>
        </div>

        <button type="button" class="btn g" style="margin-top: 16px;">Export CSV</button>
    </div>
@endsection