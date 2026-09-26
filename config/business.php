<?php

$status = ['draft','pending','approved','completed','cancelled','reversed'];
$payment = ['cash','bank_transfer','cheque','card','online','credit'];
$f = fn(string $name, string $label, string $type = 'text', bool $required = false, array $options = []) => compact('name','label','type','required','options');

return ['modules' => [
    'customers' => ['title'=>'Customers','icon'=>'people','branch_types'=>['all'],'statuses'=>['active','inactive'],'fields'=>[
        $f('customer_code','Customer Code','text',true), $f('cnic','CNIC or ID'), $f('mobile','Mobile Number','text',true),
        $f('email','Email','email'), $f('address','Address','textarea'), $f('city','City'), $f('credit_limit','Credit Limit','number')]],
    'suppliers' => ['title'=>'Suppliers and Sellers','icon'=>'truck','branch_types'=>['all'],'statuses'=>['active','inactive'],'fields'=>[
        $f('party_type','Party Type','select',true,['seller','supplier','both']), $f('cnic','CNIC or Registration Number'), $f('mobile','Mobile Number','text',true),
        $f('address','Address','textarea'), $f('bank_details','Bank Details','textarea'), $f('opening_balance','Opening Balance','number')]],
    'brokers' => ['title'=>'Third Parties and Brokers','icon'=>'handshake','branch_types'=>['car_sale_purchase'],'statuses'=>['active','inactive'],'fields'=>[
        $f('cnic','CNIC','text',true), $f('mobile','Mobile Number','text',true), $f('address','Address','textarea'),
        $f('commission_method','Commission Method','select',false,['fixed','percentage']), $f('bank_details','Bank Details','textarea')]],
    'cars' => ['title'=>'Car Inventory','icon'=>'car','branch_types'=>['car_sale_purchase'],'statuses'=>['available','reserved','sold','under_repair'],'fields'=>[
        $f('registration_no','Registration Number'), $f('make','Make','text',true), $f('model','Model','text',true), $f('variant','Variant'),
        $f('manufacturing_year','Manufacturing Year','number',true), $f('registration_year','Registration Year','number'), $f('colour','Colour','text',true),
        $f('engine_capacity','Engine Capacity'), $f('fuel_type','Fuel Type','select',true,['petrol','diesel','hybrid','electric','other']),
        $f('transmission','Transmission','select',true,['automatic','manual','other']), $f('mileage','Mileage','number'),
        $f('chassis_no','Chassis Number','text',true), $f('engine_no','Engine Number'), $f('condition','Condition','select',true,['new','used','accidental','repaired','other']),
        $f('purchase_price','Purchase Price','number',true), $f('additional_cost','Additional Cost','number'), $f('expected_sale_price','Expected Sale Price','number'),
        $f('minimum_sale_price','Minimum Sale Price','number'), $f('location','Location'), $f('notes','Notes','textarea')]],
    'car_purchases' => ['title'=>'Car Purchases','icon'=>'cart-down','branch_types'=>['car_sale_purchase'],'statuses'=>$status,'fields'=>[
        $f('seller_cnic','Seller CNIC','text',true), $f('seller_mobile','Seller Mobile','text',true), $f('car_stock_no','Car Stock Number','text',true),
        $f('car_description','Car Description','textarea',true), $f('purchase_price','Purchase Price','number',true), $f('additional_charges','Additional Charges','number'),
        $f('payment_method','Payment Method','select',true,$payment), $f('paid_amount','Paid Amount','number',true), $f('payment_due_date','Payment Due Date','date'),
        $f('agreement_reference','Agreement Reference'), $f('notes','Notes','textarea')]],
    'car_sales' => ['title'=>'Car Sales','icon'=>'cash-stack','branch_types'=>['car_sale_purchase'],'statuses'=>['draft','reserved','approved','delivered','cancelled','reversed'],'fields'=>[
        $f('customer_cnic','Customer CNIC','text',true), $f('customer_mobile','Customer Mobile','text',true), $f('car_stock_no','Car Stock Number','text',true),
        $f('purchase_cost','Car Purchase Cost','number',true), $f('sale_price','Sale Price','number',true), $f('discount','Discount','number'), $f('payment_method','Payment Method','select',true,$payment),
        $f('received_amount','Received Amount','number',true), $f('payment_due_date','Payment Due Date','date'), $f('third_party_involved','Third Party Involved','select',true,['no','yes']),
        $f('broker_name','Broker Name'), $f('broker_cnic','Broker CNIC'), $f('broker_mobile','Broker Mobile'), $f('commission_method','Commission Method','select',false,['fixed','percentage']), $f('commission_amount','Commission Amount','number'),
        $f('commission_status','Commission Status','select',false,['unpaid','partially_paid','paid']), $f('agreement_reference','Agreement Reference')]],
    'service_bookings' => ['title'=>'Service Bookings','icon'=>'calendar-check','branch_types'=>['service_booking'],'statuses'=>['pending','confirmed','arrived','cancelled','no_show'],'fields'=>[
        $f('booking_time','Booking Date and Time','datetime-local',true), $f('registration_no','Vehicle Registration','text',true), $f('vehicle','Vehicle Make and Model','text',true),
        $f('service_type','Service Type','select',true,['periodic_service','repair','inspection','detailing','other']), $f('customer_complaint','Customer Complaint','textarea',true),
        $f('pickup_required','Pickup Required','select',false,['no','yes']), $f('assigned_advisor','Assigned Advisor'), $f('estimated_duration','Estimated Duration'), $f('estimated_amount','Estimated Amount','number')]],
    'job_cards' => ['title'=>'Job Cards','icon'=>'tools','branch_types'=>['service_booking'],'statuses'=>['open','diagnosing','waiting_approval','in_progress','quality_check','ready','delivered','cancelled'],'fields'=>[
        $f('booking_no','Booking Number'), $f('registration_no','Vehicle Registration','text',true), $f('check_in_time','Check In Time','datetime-local',true),
        $f('mileage','Mileage','number',true), $f('fuel_level','Fuel Level','select',false,['empty','quarter','half','three_quarter','full']),
        $f('visible_damage','Visible Damage','textarea'), $f('customer_items','Customer Items','textarea'), $f('diagnosis','Diagnosis','textarea'),
        $f('service_tasks','Service Tasks and Labour','textarea',true), $f('parts_used','Parts Used','textarea'), $f('assigned_technician','Assigned Technician','text',true),
        $f('estimated_completion','Estimated Completion','datetime-local'), $f('customer_approval','Customer Approval','select',true,['pending','approved','rejected'])]],
    'products' => ['title'=>'Products and Stock','icon'=>'box','branch_types'=>['pos','service_booking'],'statuses'=>['active','inactive'],'fields'=>[
        $f('sku','SKU','text',true), $f('barcode','Barcode'), $f('category','Category','text',true), $f('brand','Brand'), $f('unit','Unit','select',true,['piece','box','litre','set','other']),
        $f('purchase_price','Purchase Price','number',true), $f('sale_price','Sale Price','number',true), $f('minimum_sale_price','Minimum Sale Price','number'),
        $f('tax_rate','Tax Rate Percent','number'), $f('quantity','Current Quantity','number',true), $f('reorder_level','Reorder Level','number')]],
    'pos_purchases' => ['title'=>'POS Purchases','icon'=>'bag-plus','branch_types'=>['pos'],'statuses'=>$status,'fields'=>[
        $f('supplier_invoice','Supplier Invoice Number'), $f('items','Product Lines','textarea',true), $f('other_charges','Other Charges','number'),
        $f('payment_method','Payment Method','select',true,$payment), $f('paid_amount','Paid Amount','number',true), $f('notes','Notes','textarea')]],
    'pos_sales' => ['title'=>'POS Sales','icon'=>'receipt','branch_types'=>['pos'],'statuses'=>['draft','completed','returned','cancelled'],'fields'=>[
        $f('counter','Counter','text',true), $f('items','Product Lines','textarea',true), $f('subtotal','Subtotal','number',true), $f('discount','Discount','number'),
        $f('tax','Tax','number'), $f('payment_method','Payment Method','select',true,$payment), $f('received_amount','Received Amount','number',true), $f('notes','Notes','textarea')]],
    'employees' => ['title'=>'Employees','icon'=>'person-badge','branch_types'=>['all'],'statuses'=>['active','on_leave','suspended','resigned','terminated'],'fields'=>[
        $f('employee_code','Employee Code','text',true), $f('cnic','CNIC','text',true), $f('mobile','Mobile Number','text',true), $f('address','Address','textarea'),
        $f('department','Department','text',true), $f('designation','Designation','text',true), $f('reporting_manager','Reporting Manager'), $f('joining_date','Joining Date','date',true),
        $f('employment_type','Employment Type','select',true,['permanent','contract','temporary','trainee']), $f('salary_type','Salary Type','select',true,['daily','weekly','monthly']),
        $f('basic_salary','Basic Salary or Rate','number',true), $f('bank_details','Bank Details','textarea')]],
    'attendance' => ['title'=>'Employee Attendance','icon'=>'clock-history','branch_types'=>['all'],'statuses'=>['present','absent','late','half_day','leave','holiday','off_day'],'fields'=>[
        $f('employee_code','Employee Code','text',true), $f('shift','Shift','text',true), $f('check_in','Check In','time'), $f('check_out','Check Out','time'),
        $f('late_minutes','Late Minutes','number'), $f('working_hours','Working Hours','number'), $f('overtime_hours','Overtime Hours','number'), $f('remarks','Remarks','textarea'),
        $f('approval_status','Approval Status','select',true,['pending','approved','rejected'])]],
    'payroll' => ['title'=>'Salary and Payroll','icon'=>'wallet2','branch_types'=>['all'],'statuses'=>['draft','approved','partially_paid','paid','reversed'],'fields'=>[
        $f('employee_code','Employee Code','text',true), $f('salary_type','Salary Type','select',true,['daily','weekly','monthly']), $f('period_from','Period From','date',true),
        $f('period_to','Period To','date',true), $f('basic_salary','Basic Salary / Rate','number',true), $f('worked_days','Worked Days','number'), $f('overtime_amount','Overtime Amount','number'),
        $f('allowances','Allowances','number'), $f('advances','Advances','number'), $f('attendance_deduction','Attendance Deduction','number'), $f('other_deductions','Other Deductions','number'),
        $f('net_salary','Net Salary','number',true), $f('payment_method','Payment Method','select',true,['cash','bank'])]],
    'expenses' => ['title'=>'Expenses','icon'=>'cash-coin','branch_types'=>['all'],'statuses'=>['draft','pending','approved','rejected','paid','reversed'],'fields'=>[
        $f('category','Expense Category','text',true), $f('cost_center','Cost Center'), $f('paid_to','Paid To','text',true), $f('description','Description','textarea',true),
        $f('tax_withholding','Tax or Withholding','number'), $f('payment_method','Payment Method','select',true,$payment), $f('account','Cash or Bank Account','text',true),
        $f('requested_by','Requested By','text',true), $f('approval_remarks','Approval Remarks','textarea')]],
    'payments' => ['title'=>'Receipts and Payments','icon'=>'bank','branch_types'=>['all'],'statuses'=>['draft','approved','cleared','cancelled','reversed'],'fields'=>[
        $f('voucher_type','Voucher Type','select',true,['receipt','payment','transfer','adjustment']), $f('party_type','Party Type','select',false,['customer','seller','supplier','broker','employee']),
        $f('reference_transaction','Reference Transaction'), $f('payment_method','Payment Method','select',true,$payment), $f('account','Cash or Bank Account','text',true),
        $f('transaction_no','Cheque or Transaction Number'), $f('description','Description','textarea',true)]],
]];
