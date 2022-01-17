<?php


namespace Magesales\ConvergePaymentApi\Config;

/**
 * Interface TransactionTypes.
 * @api
 */
interface TransactionTypes
{
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Credit Card Transactions

    /**
     * Credit Card Sale.
     */
    const CC_SALE = 'ccsale';

    /**
     * Credit Card Auth only.
     */
    const CC_AUTH_ONLY = 'ccauthonly';

    /**
     * Credit Card AVS only.
     */
    const CC_AVS_ONLY = 'ccavsonly';

    /**
     * Credit Card Verification.
     */
    const CC_VERIFY = 'ccverify';

    /**
     * Credit Card Return/Credit.
     */
    const CC_CREDIT = 'cccredit';

    /**
     * Credit Card Force.
     */
    const CC_FORCE = 'ccforce';

    /**
     * Credit Card Balance Inquiry.
     */
    const CC_BAL_INQUIRY = 'ccbalinquiry';

    /**
     * Generate token.
     */
    const CC_GET_TOKEN = 'ccgettoken';

    /**
     * Credit Card Return.
     */
    const CC_RETURN = 'ccreturn';

    /**
     * Credit Card Void.
     */
    const CC_VOID = 'ccvoid';

    /**
     * Credit Card Completion.
     */
    const CC_COMPLETE = 'cccomplete';

    /**
     * Credit Card Delete.
     */
    const CC_DELETE = 'ccdelete';

    /**
     * Credit Card Update Tip.
     */
    const CC_UPDATE_TIP = 'ccupdatetip';

    /**
     * Credit Card Signature.
     */
    const CC_SIGNATURE = 'ccsignature';

    /**
     * Credit Card Add Recurring Transaction.
     */
    const CC_ADD_RECURRING = 'ccaddrecurring';

    /**
     * Credit Card Update Recurring Transaction.
     */
    const CC_UPDATE_RECURRING = 'ccupdaterecurring';

    /**
     * Credit Card Delete Recurring Transaction.
     */
    const CC_DELETE_RECURRING = 'ccdeleterecurring';

    /**
     * Credit Card Submit Recurring Payment.
     */
    const CC_RECURRING_SALE = 'ccrecurringsale';

    /**
     * Credit Card Add Installment Transactions.
     */
    const CC_ADD_INSTALL = 'ccaddinstall';

    /**
     * Credit Card Update Installment Transactions.
     */
    const CC_UPDATE_INSTALL = 'ccupdateinstall';

    /**
     * Credit Card Delete Installment Transactions.
     */
    const CC_DELETE_INSTALL = 'ccdeleteinstall';

    /**
     * Credit Card Submit Installment Payment.
     */
    const CC_INSTALL_SALE = 'ccinstallsale';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Batch Import Transactions

    /**
     * Credit Card Batch Import.
     */
    const CC_IMPORT = 'ccimport';

    /**
     * Credit Card Information Batch Import.
     */
    const CC_TOKEN_IMPORT = 'cctokenimport';

    /**
     * Credit Card Recurring Batch Import.
     */
    const CC_REC_IMPORT = 'ccrecimport';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Card Manager Transactions

    /**
     * Token Query.
     */
    const CC_QUERY_TOKEN = 'ccquerytoken';

    /**
     * Token Update.
     */
    const CC_UPDATE_TOKEN = 'ccupdatetoken';

    /**
     * Token Delete.
     */
    const CC_DELETE_TOKEN = 'ccdeletetoken';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Gift Card Transactions

    /**
     * Gift Card Activation.
     */
    const EGC_ACTIVATION = 'egcactivation';

    /**
     * Gift Card Sale/Redemption.
     */
    const EGC_SALE = 'egcsale';

    /**
     * Gift Card Refund.
     */
    const EGC_CARD_REFUND = 'egccardrefund';

    /**
     * Gift Card Replenishment/Reload.
     */
    const EGC_RELOAD = 'egcreload';

    /**
     * Gift Card Balance Inquiry.
     */
    const EGC_BAL_INQUIRY = 'egcbalinquiry';

    /**
     * Gift Card Credit.
     */
    const EGC_CREDIT = 'egccredit';

    /**
     * Gift Card Generate Token.
     */
    const EGC_GET_TOKEN = 'egcgettoken';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Loyalty Card Transactions

    /**
     * Loyalty Card Enrollment.
     */
    const LT_ENROLLMENT = 'ltenrollment';

    /**
     * Loyalty Card Redemption.
     */
    const LT_REDEEM = 'ltredeem';

    /**
     * Loyalty Card Return.
     */
    const LT_RETURN = 'ltreturn';

    /**
     * Loyalty Card Add Points.
     */
    const LT_ADD_POINTS = 'ltaddpoints';

    /**
     * Loyalty Card Balance Inquiry.
     */
    const LT_INQUIRY = 'ltinquiry';

    /**
     * Loyalty Card Lead Inquiry.
     */
    const LT_LEAD_INQUIRY = 'ltleadinquiry';

    /**
     * Loyalty Card Member Inquiry.
     */
    const LT_MEMBER_INQUIRY = 'ltmemberinquiry';

    /**
     * Loyalty Card Void.
     */
    const LT_VOID = 'ltvoid';

    /**
     * Loyalty Card Delete.
     */
    const LT_DELETE = 'ltdelete';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Electronic Check Transactions

    /**
     * Electronic Check Purchase.
     */
    const ECS_PURCHASE = 'ecspurchase';

    /**
     * Electronic Check Void.
     */
    const ECS_VOID = 'ecsvoid';

    /**
     * ACH ECheck Add Recurring Transaction.
     */
    const ECS_ADD_RECURRING = 'ecsaddrecurring';

    /**
     * ACH ECheck Update Recurring Transaction.
     */
    const ECS_UPDATE_RECURRING = 'ecsupdaterecurring';

    /**
     * ACH ECheck Delete Recurring Transaction.
     */
    const ECS_DELETE_RECURRING = 'ecsdeleterecurring';

    /**
     * ACH ECheck Submit Recurring Payment.
     */
    const ECS_RECURRING_SALE = 'ecsrecurringsale';

    /**
     * ACH ECheck Add Installment Transaction.
     */
    const ECS_ADD_INSTALL = 'ecsaddinstall';

    /**
     * ACH ECheck Update Installment Transaction.
     */
    const ECS_UPDATE_INSTALL = 'ecsupdateinstall';

    /**
     * ACH ECheck Delete Installment Transaction.
     */
    const ECS_DELETE_INSTALL = 'ecsdeleteinstall';

    /**
     * ACH ECheck Submit Installment Payment.
     */
    const ECS_INSTALL_SALE = 'ecsinstallsale';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // PINLess Debit Transactions

    /**
     * PINLess Debit Purchase.
     */
    const PLD_PURCHASE = 'pldpurchase';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cash Tender Transactions

    /**
     * Cash Sale.
     */
    const CASH_SALE = 'cashsale';

    /**
     * Cash Return/Credit.
     */
    const CASH_CREDIT = 'cashcredit';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // End of Day Transactions

    /**
     * Transaction Email.
     */
    const TXN_EMAIL = 'txnemail';

    /**
     * Transaction Query.
     */
    const TXN_QUERY = 'txnquery';

    /**
     * Total/Summary.
     */
    const TOTAL = 'total';

    /**
     * Settle.
     */
    const SETTLE = 'settle';
}
