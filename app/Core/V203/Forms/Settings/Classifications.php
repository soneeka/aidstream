<?php namespace App\Core\V203\Forms\Settings;

use App\Core\Form\BaseForm;

/**
 * Class Classifications
 * @package App\Core\V202\Forms\Settings
 */
class Classifications extends BaseForm
{
    /**
     * build classifications form
     */
    public function buildForm()
    {
        $this
            ->addCheckBox('sector', trans('element.sector'), true, 'readonly')
            ->addCheckBox('tag', trans('element.tag'))
            ->addCheckBox('policy_marker', trans('element.policy_marker'))
            ->addCheckBox('collaboration_type', trans('element.collaboration_type'))
            ->addCheckBox('default_flow_type', trans('element.default_flow_type'))
            ->addCheckBox('default_finance_type', trans('element.default_finance_type'))
            ->addCheckBox('default_aid_type', trans('element.default_aid_type'))
            ->addCheckBox('default_tied_status', trans('element.default_tied_status'))
            ->addCheckBox('country_budget_items', trans('element.country_budget_items'))
            ->addCheckBox('humanitarian_scope', trans('element.humanitarian_scope'));
    }
}
