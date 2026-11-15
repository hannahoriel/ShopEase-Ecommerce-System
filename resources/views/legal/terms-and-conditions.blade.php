@extends('legal.layout')

@section('pageTitle', 'ShopEase '.$termsRoleLabel.' Terms and Conditions')
@section('documentType', 'Terms & Conditions')
@section('heading', $termsRoleLabel.' Terms and Conditions')
@section('summary', 'Review the platform rules that apply to every ShopEase account and the additional responsibilities for your account role.')
@section('documentVersion', $termsVersion)
@section('documentKey', 'terms')
@section('legalRole', $legalRole)
@section('termsUrl', route('terms-and-conditions', ['role' => $termsRole, 'return_to' => $hasRegistrationReturn ? $legalReturnUrl : null]))
@section('privacyUrl', route('privacy-policy', ['role' => $termsRole, 'return_to' => $hasRegistrationReturn ? $legalReturnUrl : null]))

@section('toc')
    <a href="#terms-agreement">1. Agreement and eligibility</a>
    <a href="#terms-orders">2. Orders and payments</a>
    @if ($termsRole === 'buyer')
        <a href="#terms-buyer">3. Buyer-specific terms</a>
    @elseif ($termsRole === 'seller')
        <a href="#terms-seller">3. Seller-specific terms</a>
    @elseif ($termsRole === 'logistics')
        <a href="#terms-logistics">3. Logistics-specific terms</a>
    @elseif ($termsRole === 'rider')
        <a href="#terms-rider">3. Rider-specific terms</a>
    @elseif ($termsRole === 'courier')
        <a href="#terms-courier">3. Courier-specific terms</a>
    @endif
    <a href="#terms-enforcement">4. Complaints and account actions</a>
    <a href="#terms-rights">5. Legal rights</a>
@endsection

@section('content')
    <div class="document-notice"><strong>For {{ strtolower($termsRoleLabel) }} accounts.</strong> The general platform terms below apply to every account. Only the {{ strtolower($termsRoleLabel) }}-specific terms are shown on this page. Registration remains subject to verification and approval where applicable. Check each section after reading; the Terms checkbox is selected when all sections on this page are checked. The Privacy Policy checkbox is tracked separately.</div>
    <p class="legal-progress" data-legal-progress role="status" aria-live="polite">Mark each section as you read. Your progress is saved in this browser.</p>

    <section id="terms-agreement">
        <h2>1. Agreement, scope, and account eligibility</h2>
        <ul>
            <li>These Terms apply to buyer, seller, logistics partner, rider, and courier accounts and to each person's use of ShopEase websites, applications, APIs, ordering, messaging, wallet, complaint, parcel-tracking, and delivery functions. Role-specific provisions apply in addition to the common terms.</li>
            <li>At registration, you must separately check the box agreeing to these Terms and the box acknowledging the <a href="@yield('privacyUrl')">Privacy Policy</a>. ShopEase records the date and time, account role, and version of each document accepted. If you do not agree, do not submit the registration. Acknowledgment of the Privacy Policy is notice of data processing; it is not a waiver of privacy rights.</li>
            <li>You must be at least 18, legally able to enter into the applicable agreement, and authorized to act for any business you register. You may not create an account using another person's identity or share an account to bypass verification. A business representative confirms authority to submit its information and documents.</li>
            <li>Provide complete and accurate details and documents requested for your role, including identity, contact, business, address, vehicle, license, permit, and service-area information where applicable. Keep them current and notify ShopEase of material changes. ShopEase may verify submitted information and request clarifications.</li>
            <li>Applications may be marked pending while ShopEase, an administrator, delivery hub, logistics partner, or sorting center reviews them. Submitting an application does not mean it is approved or authorize you to sell, accept orders, handle parcels, or make deliveries. Approval may be limited to a particular role, hub, center, or operating area.</li>
            <li>Keep passwords and verification codes confidential, use a unique password, log out on shared devices, and report suspected account compromise promptly through ShopEase support. Do not impersonate others, probe or disrupt the service, bypass access controls, upload malicious or unlawful material, manipulate reviews or orders, collude on complaints, or use the platform for fraud, harassment, discrimination, or unlawful conduct.</li>
            <li>Use order, payment, complaint, message, parcel scan, pickup, proof-of-delivery, return, and remittance functions honestly. Do not falsify evidence, timestamps, locations, delivery attempts, inventory, balances, or account details. Cooperate in good-faith investigations and do not retaliate against a user for a genuine complaint.</li>
            <li>Personal information and shipment details are confidential. Access or use them only as necessary to perform your role, keep them secure, do not copy, sell, disclose, or use them for unrelated marketing, and delete or return them when no longer needed unless law or an open dispute requires retention. See the Privacy Policy for how ShopEase processes information.</li>
            <li>ShopEase may change, suspend, or discontinue a feature, and may display delivery estimates that depend on sellers, partners, routes, and conditions outside its control. A displayed estimate is not a guarantee unless a specific commitment is expressly made for the transaction. This does not limit mandatory rights under law.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-agreement', 'message' => 'I have read and agree to Section 1 of these Terms and Conditions.'])
    </section>

    <section id="terms-orders">
        <h2>2. Orders, platform payments, and general account actions</h2>
        <ul>
            <li>ShopEase facilitates transactions between buyers and sellers and coordinates platform and delivery workflows. Unless expressly stated otherwise, the seller remains responsible for its products, product descriptions, fulfillment, and applicable seller obligations; the carrier or logistics provider remains responsible for its assigned transport service.</li>
            <li>Order acceptance, cancellation, delivery, return, complaint, and refund status are determined from the order record and the applicable flow shown in ShopEase. An order status may update as users or systems record events. If information appears incorrect, promptly contact support and preserve relevant evidence.</li>
            <li>Available payment methods and charges are those shown at checkout or in the relevant account. ShopEase does not promise that a payment, wallet credit, seller balance, or rider earning is withdrawable unless a withdrawal function and its terms are expressly provided.</li>
            <li>Submit complaints and disputes through the available order or support channels, with accurate descriptions, dates, photographs, receipts, messages, and other relevant evidence. ShopEase may ask each side for a response, review available platform records, and decide platform remedies. Platform review does not prevent either party from exercising lawful remedies with a regulator or court.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-orders', 'message' => 'I have read and agree to Section 2 of these Terms and Conditions.'])
    </section>

    @if ($termsRole === 'buyer')
    <section id="terms-buyer" class="role-section">
        <h2>3. Buyer-specific terms</h2>
        <ul>
            <li>Keep your account, contact number, and delivery address accurate and accessible. Review seller details, item description, variants, quantity, price, shipping charges, vouchers, and payment method before confirming. You are responsible for checking that your order is correct before submission.</li>
            <li>Orders are subject to product availability, seller acceptance where applicable, payment status, and platform checks. Do not place fake, duplicate, abusive, or knowingly unserviceable orders. For Cash on Delivery (COD), pay the amount shown for the order to the authorized delivery representative upon delivery; do not pay an unrelated person or off-platform account.</li>
            <li>Keep your phone available, provide reasonable delivery access, and coordinate through ShopEase channels. A failed delivery, missed recipient, incorrect address, or refusal may cause delay, return, cancellation, or fees only as shown in the applicable flow and permitted by law. Check parcel condition and report a missing, wrong, damaged, or undelivered order promptly through the order's complaint or return process.</li>
            <li>Return, cancellation, and refund eligibility depends on the order's status, evidence, stated procedure, seller response, and applicable law. A request is not automatically approved. Do not submit fabricated evidence, misuse return or refund tools, conceal relevant facts, or seek duplicate recovery for the same loss. Good-faith complaints are permitted and will not by themselves establish wrongdoing.</li>
            <li><strong>ShopEase Wallet refunds.</strong> Where an approved refund is issued as a ShopEase Wallet credit, that credit is a platform balance for eligible future ShopEase orders. The wallet supports order payments and refund/payment-reversal entries; it does not provide a buyer cash-out, bank withdrawal, transfer-to-another-user, or cash conversion feature. The available balance and whether it can cover an order are shown in the wallet and at checkout. A wallet-funded order may require enough balance for the whole order, as shown at checkout.</li>
            <li>The wallet-only treatment above applies only where it is lawful for the particular refund and transaction. It does not remove any mandatory right to a cash refund, payment reversal, or other remedy under Philippine consumer or internet-transaction law. If law requires a different refund method, that requirement controls. Ask ShopEase support if the displayed refund method appears inconsistent with your legal rights.</li>
            <li>Use vouchers and promotions only under the eligibility, expiry, order minimum, and other conditions displayed with them. ShopEase may cancel a promotion or correct an erroneous benefit where allowed, after reviewing the transaction.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-buyer', 'message' => 'I have read and agree to Section 3, Buyer-specific terms.'])
    </section>
    @endif

    @if ($termsRole === 'seller')
    <section id="terms-seller" class="role-section {{ $termsRole === 'seller' ? 'current' : '' }}">
        <h2>3. Seller-specific terms</h2>
        <ul>
            <li>Submit accurate identity, business, permit, tax, and contact information and maintain any licenses required for your business and products. You are responsible for applicable taxes, product safety, consumer disclosures, warranties, and legal compliance.</li>
            <li>List only goods you are authorized to sell. Make product title, photographs, condition, specifications, variations, price, stock, shipping details, origin, and safety/usage information accurate and current. Do not list counterfeit, stolen, recalled, restricted, unsafe, illegal, or materially misrepresented goods. Remove or correct a listing promptly if its information or availability changes.</li>
            <li>Maintain inventory, honor confirmed orders, prepare parcels safely and securely, and meet the preparation and pickup details shown in the seller workflow. Record readiness and handoff truthfully. Do not substitute, omit, tamper with, or falsely mark an item as shipped or fulfilled.</li>
            <li>Respond professionally to buyer messages, return/refund requests, and complaints. Provide relevant order, product, packing, and dispatch evidence when requested. Do not pressure or reward a buyer to withdraw a valid complaint, retaliate, or take an order off-platform to avoid ShopEase records or safeguards.</li>
            <li>Return, refund, and complaint outcomes are handled through the applicable order flow and applicable law. Cooperate with review and do not destroy or alter relevant records. A refund or complaint decision may affect seller settlement or wallet ledger entries as shown in the seller account.</li>
            <li><strong>Compliance Score.</strong> The current score is calculated from 100 points, less 5 points for each non-archived product currently marked pending/under review/review, and less 0.5 points for each complaint finally recorded as seller-at-fault. The score is floored at 0. An allegation alone is not an at-fault finding; a complaint deduction should be based on the recorded investigation outcome. ShopEase recalculates the score from those records, so review-state changes can affect it.</li>
            <li>Before a complaint is finalized as seller-at-fault, the seller should be notified of the issue and have a reasonable opportunity to respond with evidence. A seller who disagrees with a finding or score may contact ShopEase support and provide case details for review. ShopEase may correct a decision or score when the underlying record is changed.</li>
            <li><strong>Score-based suspension.</strong> If the calculated score falls below 75%, the seller account is suspended for 3 days. A score of exactly 75% does not trigger this threshold. During suspension, access to seller functions may be restricted; existing orders, buyer safety, records, and legally required payments remain subject to review. After the applicable period, account status may be restored under the system's reactivation rules, subject to any separate restriction or unresolved legal issue.</li>
            <li>Seller wallet and settlement entries are records of amounts earned, adjustments, commissions, refunds, and other configured transactions; an entry is not necessarily a completed bank payout. Review your ledger and contact ShopEase support promptly about an unexplained entry.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-seller', 'message' => 'I have read and agree to Section 3, Seller-specific terms.'])
    </section>
    @endif

    @if ($termsRole === 'logistics')
    <section id="terms-logistics" class="role-section {{ $termsRole === 'logistics' ? 'current' : '' }}">
        <h2>3. Logistics-specific terms</h2>
        <ul>
            <li>Maintain accurate company and representative details, service areas, business registrations, permits, required insurance, facilities, equipment, and operational contact information. Submit documents requested during review and promptly report expired permits, operational changes, safety incidents, or a change in authority.</li>
            <li>Use the assigned hub, center, fleet, route, manifest, and parcel-management functions only for authorized operations. Confirm parcel identity, quantity, visible condition, and custody at each handoff; complete required scans and status updates accurately and promptly. Never bypass a scan, alter a manifest, open a parcel without authorization, or mark a parcel transferred or delivered before it is.</li>
            <li>Secure parcels against loss, damage, theft, tampering, unauthorized access, weather, and unsafe storage. Report discrepancies, damaged packaging, suspected tampering, missing parcels, delivery failures, and routing or system errors through the designated dispute or support process as soon as reasonably possible. Preserve relevant scans, photos, manifests, and communications.</li>
            <li>Lost or damaged parcels and seller reimbursement. If a report confirms that a parcel was lost or damaged while in your company’s assigned custody, your company is responsible for the loss and must reimburse the seller for the verified merchandise value of the affected order; shipping charges are excluded. ShopEase will determine responsibility through the parcel issue review process using custody scans, manifests, evidence, and responses from the parties. Any reimbursement, partner charge, or settlement will be recorded through the applicable ShopEase workflow and remains subject to applicable law and any separate logistics partner agreement.</li>
            <li>Assign only appropriately authorized and qualified riders/couriers, keep their credentials and vehicle information current, and give them safe parcel-handling, privacy, customer-conduct, proof, and incident-reporting instructions. Do not share buyer or seller data outside the assigned logistics purpose.</li>
            <li>Reconcile manifests, transfers, COD collections, remittances, rider earnings, and logistics reports using the available records. Report discrepancies; do not divert, retain, or misstate funds or parcels. Any partner fee, service level, deduction, or settlement term not specified here must be agreed in the applicable partner arrangement and remain lawful.</li>
            <li>Follow assigned schedules and provide prompt notice when capacity, staff, vehicle, weather, facility, safety, or technical conditions prevent a planned handoff. Repeated unexcused failures, falsified scans, mishandling, or failure to cooperate with a parcel investigation may result in reassignment, restrictions, suspension, or termination under the administrator-enforcement section.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-logistics', 'message' => 'I have read and agree to Section 3, Logistics-specific terms.'])
    </section>
    @endif

    @if ($termsRole === 'rider')
    <section id="terms-rider" class="role-section {{ $termsRole === 'rider' ? 'current' : '' }}">
        <h2>3. Rider-specific terms</h2>
        <ul>
            <li>Keep your identity, contact details, vehicle description, plate number, driver's license, and hub assignment accurate and current. Maintain the qualifications and roadworthy vehicle required by law and your logistics partner. Follow traffic laws, safety instructions, and lawful route and parcel-handling procedures.</li>
            <li>Accept and complete only assigned pickup, delivery, and return tasks. Check the order/tracking identity and visible parcel condition, maintain secure custody, use required scans and proof, and report damage, mismatch, access problems, recipient unavailability, emergency, or unsafe conditions promptly. Do not falsely claim pickup, delivery, cash collection, or a failed attempt.</li>
            <li>For delivery and return tasks, follow the instructions shown in the rider app, including required photo proof or other supported evidence. Do not photograph or disclose more personal information than required, leave parcels unattended without authorization, open or tamper with parcels, abandon a route, or use buyer contact details for personal purposes.</li>
            <li><strong>Seller-scheduled pickup and third missed attempt.</strong> A seller pickup is recorded as a missed attempt when its scheduled date has passed, the parcel has not been picked up, and the order and scheduled time still match the active assignment. The system tracks up to 3 seller-scheduled pickup attempts for an order. After the third scheduled attempt is recorded as missed, the order may be cancelled and a one-time ₱20.00 pickup-no-show entry may be posted to the earnings ledger for the rider assigned to that third attempt.</li>
            <li>The ₱20.00 amount is an earnings-ledger adjustment, not an unrestricted authority to deduct wages. It may be applied only when lawful for the rider's employment/contractor arrangement, consistent with a separate applicable agreement and required labor protections. A database entry or click-to-accept alone does not waive statutory wage rights. ShopEase and its logistics partners must review the legal basis, provide notice and a way to dispute an incorrect assignment or missed-attempt record, and correct an improper entry. Urgent safety incidents, seller unavailability, system faults, reassignment, or other conditions outside a rider's control should be reported promptly for review.</li>
            <li>Rider compensation and remittance depend on the applicable logistics arrangement and posted earning records. Review the earnings ledger and report discrepancies to the responsible logistics partner or ShopEase support. Do not collect unauthorized fees or keep COD/remittance funds beyond the applicable settlement instructions.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-rider', 'message' => 'I have read and agree to Section 3, Rider-specific terms.'])
    </section>
    @endif

    @if ($termsRole === 'courier')
    <section id="terms-courier" class="role-section {{ $termsRole === 'courier' ? 'current' : '' }}">
        <h2>3. Courier-specific terms</h2>
        <ul>
            <li>Courier applications are subject to sorting-center review. Provide accurate identity, phone, address, vehicle, plate, and driver's-license information and any documents required for the selected transport assignment. Do not begin work until the relevant account and assignment are approved.</li>
            <li>Follow the assigned manifest, route, transport schedule, loading/unloading instructions, vehicle requirements, and sorting-center handoff procedure. Verify parcel counts and condition at acceptance and delivery, scan or reconcile parcels when required, and maintain the chain of custody for the entire assigned leg.</li>
            <li>Protect parcels from theft, loss, damage, weather, unauthorized opening, and tampering. Do not divert a parcel, falsify a scan or manifest, leave parcels unsecured, or disclose route or shipment details to unauthorized people. Promptly report shortages, discrepancies, delays, damage, unsafe conditions, vehicle problems, or an unsuccessful handoff and preserve supporting records.</li>
            <li>Use personal information only as necessary for assigned transport and protect it from unauthorized disclosure. If unable to attend or complete a route, notify the sorting center or logistics partner as early as possible and follow their instructions. Repeated unexcused absences, mishandling, false records, or failure to cooperate may lead to route reassignment or account review.</li>
            <li>These Terms do not create a fixed courier-specific monetary deduction. Any compensation or deduction must be stated in a separate applicable agreement, be accurately documented, and comply with law.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-courier', 'message' => 'I have read and agree to Section 3, Courier-specific terms.'])
    </section>
    @endif

    <section id="terms-enforcement">
        <h2>4. Complaints, administrator actions, and review</h2>
        <ul>
            <li>ShopEase administrators may monitor activity reasonably necessary to operate the service, address safety and fraud risks, investigate complaints, verify accounts, and enforce these Terms and applicable policies. Access to account, order, financial, and delivery records should be limited to authorized operational purposes.</li>
            <li>Where there is a reasonable, documented basis to believe a user has violated these Terms, another published policy, applicable law, or a role obligation—or presents a material safety, fraud, privacy, or operational risk—an administrator may issue a warning, require corrective information, restrict a feature, hold access to a task pending review, suspend an account temporarily, or terminate access for serious or repeated conduct.</li>
            <li>Actions should be proportionate, based on available evidence, and applied consistently. Where practicable, ShopEase will tell the affected user the reason and provide a means to respond or request review. An urgent restriction may be applied first to prevent ongoing harm, fraud, evidence destruction, or unauthorized access, with an opportunity to respond as soon as reasonably practicable. Users may contact <a href="mailto:support@shopease.com">support@shopease.com</a> with the account and case details.</li>
            <li>An account restriction does not automatically erase a user's lawful rights, order/payment obligations, consumer remedies, earned compensation, or privacy rights. Any hold or offset involving money must have a lawful basis and be separately reviewed. Do not use a suspension to retaliate against a good-faith complaint.</li>
            <li>ShopEase may revise these Terms by publishing an updated version and effective date. Material changes will be brought to users' attention. Where renewed acceptance is required, the updated version must be accepted before continued access to affected features. The account records the currently recorded accepted version and timestamp.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-enforcement', 'message' => 'I have read and agree to Section 4 of these Terms and Conditions.'])
    </section>

    <section id="terms-rights">
        <h2>5. Legal rights and governing requirements</h2>
        <ul>
            <li>These Terms do not exclude or limit any consumer, privacy, labor, electronic-commerce, or other protection that cannot lawfully be excluded or limited. If a provision conflicts with mandatory law, the mandatory requirement controls and the rest of the Terms remain in effect to the extent permitted.</li>
            <li>ShopEase policies are not a substitute for a seller, logistics-partner, rider, or courier service agreement where the parties' relationship requires a separate agreement. Any employment classification, wage, benefit, deduction, tax, insurance, or contractor obligation must be determined under the facts and applicable law, not solely by an account label.</li>
            <li>These Terms and platform disputes are subject to applicable laws of the Philippines. Users may also pursue remedies before the competent regulator, agency, or court as provided by law.</li>
        </ul>
        @include('legal.partials.section-acknowledgment', ['sectionId' => 'terms-rights', 'message' => 'I have read and agree to Section 5 of these Terms and Conditions.'])
    </section>

    <footer class="document-end">By accepting, you confirm that you could review the common terms and the section for your account role. For account questions, contact <a href="mailto:support@shopease.com">support@shopease.com</a>. For data handling, see the <a href="@yield('privacyUrl')">Privacy Policy</a>.</footer>
@endsection
