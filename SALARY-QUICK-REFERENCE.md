# Quick Reference: Salary Increments & Allowances

## 🚀 What's New

### Bug Fixed
✅ Salary advance submission error - Fixed database constraint issue

### Features Added
✅ 15 new salary components (allowances & deductions)
✅ Automated salary increment system
✅ Increment history tracking
✅ Real-time calculation preview

---

## 📋 Available Salary Components (21 Total)

### Allowances (Earnings)
```
✓ House Rent Allowance (HRA)
✓ Transport Allowance (TRANSPORT)
✓ Medical Allowance (MEDICAL)
✓ Food Allowance (FOOD) - NEW
✓ Mobile/Phone Allowance (PHONE) - NEW
✓ Fuel Allowance (FUEL) - NEW
✓ Conveyance Allowance (CONVEYANCE) - NEW
✓ Internet Allowance (INTERNET) - NEW
✓ Special Allowance (SPECIAL) - NEW
✓ Education Allowance (EDUCATION) - NEW
✓ Entertainment Allowance (ENTERTAINMENT) - NEW
✓ Overtime Allowance (OVERTIME) - NEW
✓ Performance Bonus (PERF_BONUS) - NEW
✓ Annual Bonus (ANNUAL_BONUS) - NEW
✓ Leave Encashment (LEAVE_ENCASH) - NEW
```

### Deductions
```
✓ Income Tax (INCOME_TAX)
✓ Provident Fund (PF)
✓ EOBI
✓ Professional Tax (PROF_TAX) - NEW
✓ Late Deduction (LATE_DED) - NEW
✓ Absence Deduction (ABSENCE_DED) - NEW
```

---

## 🎯 How to Apply Increment

### Step-by-Step
1. Go to **Employees** → Select employee
2. Click **"Manage Salary"**
3. Click **"Apply Increment"** button
4. Fill the form:
   - **Type**: Percentage or Fixed Amount
   - **Value**: Enter percentage (e.g., 10) or amount (e.g., 5000)
   - **Date**: When increment takes effect
   - **Remarks**: Reason for increment
5. Review the **preview** (shows new salary)
6. Click **"Apply Increment"**
7. Confirm in dialog
8. ✓ Done! New salary active from effective date

### Increment Types Explained

**Percentage Increment** (Recommended for annual raises)
```
Example: 10% increment on Rs. 50,000
Basic: Rs. 50,000 → Rs. 55,000 (+10%)
HRA: Rs. 10,000 → Rs. 11,000 (+10%)
Transport: Rs. 5,000 → Rs. 5,500 (+10%)
All allowances increase by 10%
```

**Fixed Amount Increment** (For promotions)
```
Example: Rs. 5,000 increment on Rs. 50,000
Basic: Rs. 50,000 → Rs. 55,000
Allowances increase proportionally
Total increase = Rs. 5,000
```

---

## 💼 How to Add Allowances

### For New Employee
1. Create employee first
2. Go to **"Manage Salary"**
3. Enter **Basic Salary**
4. Click **"+ Add Component"**
5. Select allowance (e.g., Food Allowance)
6. Enter amount (e.g., 5000)
7. Repeat for each allowance
8. Review **Gross Salary** (auto-calculated)
9. Click **"Save Salary Structure"**

### For Existing Employee
Same process - will create new salary record with current date

---

## 📊 Common Allowance Examples (Pakistan)

| Allowance | Typical Amount | Taxable |
|-----------|---------------|---------|
| House Rent Allowance | 40-50% of basic | No |
| Transport | Rs. 3,000 - 8,000 | No |
| Medical | Rs. 2,000 - 5,000 | No |
| Food | Rs. 3,000 - 6,000 | No |
| Phone/Mobile | Rs. 2,000 - 5,000 | No |
| Fuel | Rs. 5,000 - 15,000 | No |
| Internet | Rs. 1,500 - 3,000 | No |

---

## 🔍 Viewing Increment History

### In Employee Details
1. Go to employee profile
2. Scroll to **"Salary Information"**
3. View **"Increment History"** section
4. See all past increments with:
   - Previous salary
   - New salary
   - Increment amount & percentage
   - Effective dates
   - Remarks

---

## 🆘 Troubleshooting

### "No active salary found"
→ Employee needs salary setup first (use "Add New Salary Structure")

### Increment not showing
→ Check effective date - increment active only from that date

### Wrong calculation
→ Preview shows exact calculation before applying

### Can't submit salary advance
→ Bug is fixed! Try again with any advance type

---

## 📞 Quick Actions

| Task | Location | Button |
|------|----------|--------|
| Apply Increment | Employee → Manage Salary | "Apply Increment" |
| Add Allowances | Employee → Manage Salary | "+ Add Component" |
| View History | Employee Details | "Increment History" |
| Request Advance | Salary Advances | "Request Advance" |

---

## 💡 Tips

✓ **Annual Increments**: Use percentage (typically 5-15%)

✓ **Promotions**: Use fixed amount for significant raises

✓ **Effective Date**: Set to first day of month for clean payroll

✓ **Remarks**: Always add reason for audit trail

✓ **Preview**: Double-check preview before applying

✓ **Allowances**: Add all applicable allowances during setup

✓ **History**: Review history before new increment to avoid duplicates

---

## 🔐 Access Control

| Role | Can Apply Increment | Can Add Allowances | Can View History |
|------|-------------------|-------------------|-----------------|
| Admin | ✅ All employees | ✅ All employees | ✅ All employees |
| Manager | ✅ Team members | ✅ Team members | ✅ Team members |
| Employee | ❌ No | ❌ No | ✅ Own only |

---

## 📅 Best Practices

1. **Annual Review**: Apply increments during annual review cycle
2. **Documentation**: Always add meaningful remarks
3. **Consistency**: Use same increment type across team for fairness
4. **Effective Dates**: Coordinate with payroll processing dates
5. **Allowances**: Review and update allowances annually
6. **History**: Keep complete history for compliance and audits

---

## API Reference

### Apply Increment
```bash
POST /api/salary-components/employees/{id}/apply-increment
Authorization: Bearer {token}
Content-Type: application/json

{
  "increment_type": "percentage",
  "increment_value": 10,
  "effective_from": "2026-03-01",
  "remarks": "Annual increment 2026"
}
```

### Get Increment History
```bash
GET /api/salary-components/employees/{id}/increment-history
Authorization: Bearer {token}
```

### Get Salary Components List
```bash
GET /api/salary-components
Authorization: Bearer {token}
```

---

**Last Updated**: February 21, 2026
**Status**: ✅ Production Ready
