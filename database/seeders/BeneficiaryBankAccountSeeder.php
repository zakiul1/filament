<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeneficiaryBankAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Beneficiary companies
        $siatexId = DB::table('beneficiary_companies')->where('short_name', 'SIATEX')->value('id');
        $aptexId = DB::table('beneficiary_companies')->where('short_name', 'APTEX')->value('id');
        $dottaId = DB::table('beneficiary_companies')->where('short_name', 'DOTTA')->value('id');

        // Currencies
        $bdtId = DB::table('currencies')->where('code', 'BDT')->value('id');
        $usdId = DB::table('currencies')->where('code', 'USD')->value('id');
        $eurId = DB::table('currencies')->where('code', 'EUR')->value('id');

        // Bank branches (must match BankBranchSeeder values)
        $hsbcBranchId = DB::table('bank_branches')
            ->join('banks', 'bank_branches.bank_id', '=', 'banks.id')
            ->where('banks.short_name', 'HSBC')
            ->where('bank_branches.name', 'Gulshan Branch')
            ->value('bank_branches.id');

        $scbBranchId = DB::table('bank_branches')
            ->join('banks', 'bank_branches.bank_id', '=', 'banks.id')
            ->where('banks.short_name', 'SCB')
            ->where('bank_branches.name', 'Motijheel Branch')
            ->value('bank_branches.id');

        $cityBranchId = DB::table('bank_branches')
            ->join('banks', 'bank_branches.bank_id', '=', 'banks.id')
            ->where('banks.short_name', 'CITY')
            ->where('bank_branches.name', 'Dhanmondi Branch')
            ->value('bank_branches.id');

        $accounts = [];

        // SIATEX - BDT LC/TT default account (City Bank Dhanmondi)
        if ($siatexId && $cityBranchId && $bdtId) {
            $accounts[] = [
                'beneficiary_company_id' => $siatexId,
                'bank_branch_id' => $cityBranchId,
                'currency_id' => $bdtId,

                'account_title' => 'SIATEX (BD) LTD',
                'account_number' => '0101234567890',
                'iban' => null,
                'swift_code' => 'CIBLBDDH',    // override / same as branch
                'routing_number' => null,

                'is_lc_account' => true,
                'is_tt_account' => true,
                'is_default' => true,          // default BDT account for SIATEX
                'is_active' => true,
                'notes' => 'Primary BDT LC/TT account for SIATEX.',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // SIATEX - USD LC account (HSBC Gulshan)
        if ($siatexId && $hsbcBranchId && $usdId) {
            $accounts[] = [
                'beneficiary_company_id' => $siatexId,
                'bank_branch_id' => $hsbcBranchId,
                'currency_id' => $usdId,

                'account_title' => 'SIATEX (BD) LTD',
                'account_number' => '123456789USD',
                'iban' => null,
                'swift_code' => 'HSBCCATT',
                'routing_number' => null,

                'is_lc_account' => true,
                'is_tt_account' => false,
                'is_default' => true,          // default USD account for SIATEX
                'is_active' => true,
                'notes' => 'USD LC account for major buyers.',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // APTEX - USD LC/TT account (Standard Chartered Motijheel)
        if ($aptexId && $scbBranchId && $usdId) {
            $accounts[] = [
                'beneficiary_company_id' => $aptexId,
                'bank_branch_id' => $scbBranchId,
                'currency_id' => $usdId,

                'account_title' => 'APTEX FASHION LTD',
                'account_number' => 'SCB-APTEX-USD-001',
                'iban' => null,
                'swift_code' => 'SCBLBDDX',
                'routing_number' => null,

                'is_lc_account' => true,
                'is_tt_account' => true,
                'is_default' => true,
                'is_active' => true,
                'notes' => 'Main USD account for APTEX.',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // DOTTA - EUR LC account (reuse HSBC Gulshan for example)
        if ($dottaId && $hsbcBranchId && $eurId) {
            $accounts[] = [
                'beneficiary_company_id' => $dottaId,
                'bank_branch_id' => $hsbcBranchId,
                'currency_id' => $eurId,

                'account_title' => 'DOTTA TEX LTD',
                'account_number' => 'DOTTA-EUR-001',
                'iban' => null,
                'swift_code' => 'HSBCCATT',
                'routing_number' => null,

                'is_lc_account' => true,
                'is_tt_account' => false,
                'is_default' => true,
                'is_active' => true,
                'notes' => 'Preferred EUR LC account for DOTTA TEX.',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($accounts)) {
            DB::table('beneficiary_bank_accounts')->insert($accounts);
        }
    }
}