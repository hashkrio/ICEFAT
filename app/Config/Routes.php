<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(false);
$routes->post('login/authenticate', 'LoginController::authenticate', ['as' => 'user.login.auth']);
$routes->get('login', 'LoginController::index', ['as' => 'user.login.index']);
$routes->get('login/logout', 'LoginController::logout', ['as' => 'user.logout']);

service('auth')->routes($routes, ['except' => ['login']]);

$routes->group('', ['filter' => \App\Filters\Auth::class], static function ($route) {
    $route->get('home', 'Home::index', ['as' => 'user.home.index']);
    $route->get('add', 'Home::add', ['as' => 'add.calculations']);
    $route->get('users', 'Home::users', ['as' => 'user.lists']);
    $route->get('material/quantity', 'Home::getMaterialByQuantity');
    $route->get('crate/design', 'Home::getMaterialByCrateDesign');
    $route->get('design/regression', 'Home::getDesignRegression');
    $route->get('transportation/emission', 'Home::getTransportEmissionFactor');
    $route->get('crate/type', 'Home::getCrateType');
    $route->get('model/option', 'Home::getModelOption');
});

$routes->group('filter', static function ($rt) {
    $rt->post('set_default_response', 'CommonController::setDefaultResponse', ['as' => 'ajax.default.response']);
    $rt->match(['GET', 'POST'], 'user_list', 'CommonController::getUserLists', ['as' => 'ajax.users.lists']);
    $rt->match(['PUT', 'PATCH', 'DELETE'], 'user_list', 'CommonController::updateOrCreateUser', ['as' => 'ajax.update.create.user']);
    $rt->delete('delete_user/(:any)', 'CommonController::deleteUser/$1', ['as' => 'ajax.update.delete.user']);
    $rt->post('modal_options', 'CommonController::getModelOptions', ['as' => 'ajax.modal.options',]);
    $rt->post('caret_design', 'CommonController::getCaretDesigns', ['as' => 'ajax.crate.design']);
    $rt->post('caret_weight/(:num)/weight/(:num)', 'CommonController::getCaretWeight/$1/$2', ['as' => 'ajax.crate.weight']);
    $rt->post('get_values_by_crate_type', 'CommonController::getValuesByCrateType', ['as' => 'ajax.crate.values.by.create']);
    $rt->post('carbon_footprint/(:num)/air_passanger/(:num)/air_freight/(:num)/road_freight/(:num)/sea_freight/(:num)', 'CommonController::carbonFootPrintValue/$1/$2/$3/$4/$5', ['as' => 'ajax.carbon.footprint']);
    $rt->post('save', 'CommonController::saveCalculations', ['as' => 'ajax.carbon.footprint.save']);
    $rt->post('save_new', 'CommonController::saveNewCalculations', ['as' => 'ajax.carbon.footprint.new.save']);
    $rt->post('all_data_list', 'CommonController::allDataList', ['as' => 'ajax.calculation.all.data.list']);
    $rt->put('all_data_list', 'CommonController::deleteAllDataListRecord', ['as' => 'ajax.calculation.all.data.delete.all']);
    $rt->delete('all_data_list', 'CommonController::deleteDataListRecord', ['as' => 'ajax.calculation.all.data.delete.list']);
    $rt->post('calculation_list_data', 'CommonController::calculationListData', ['as' => 'ajax.calculation.list.data']);
    $rt->post('calculation_list_data/details/(:num)', 'CommonController::calculationListDataDetails/$1', ['as' => 'ajax.calculation.list.data.details']);
    $rt->post('material_quantity_list_data', 'CommonController::materialQuantityListData', ['as' => 'ajax.material.quantity.list.data']);
    $rt->put('material_quantity_list_data', 'CommonController::materialQuantityUpdateData', ['as' => 'ajax.material.quantity.update.data']);
    $rt->post('material_quantity_list_data/details/(:any)', 'CommonController::materialQuantityListDataDetails/$1', ['as' => 'ajax.material.quantity.list.data.details']);
    $rt->post('crate_design_list_data', 'CommonController::crateDesignListData', ['as' => 'ajax.crate.design.list.data']);
    $rt->post('design_regression_list_data', 'CommonController::designRegressionListData', ['as' => 'ajax.design.regression.list.data']);
    $rt->put('design_regression_list_data', 'CommonController::designRegressionUpdateData', ['as' => 'ajax.design.regression.update.data']);
    $rt->post('transportation_emission_factor_list_data', 'CommonController::tranportationEmissionFactorListData', ['as' => 'ajax.transporation.emission.factor.list.data']);
    $rt->put('transportation_emission_factor_list_data', 'CommonController::tranportationEmissionFactorUpdateData', ['as' => 'ajax.transporation.emission.factor.update.data']);
    $rt->post('crate_type_list_data', 'CommonController::crateTypeListData', ['as' => 'ajax.crate.type.list.data']);
    $rt->post('model_option_list_data', 'CommonController::modelOptionListData', ['as' => 'ajax.model.option.list.data']);
    $rt->post('crate_type_primary_save', 'CommonController::crateTypePrimarySave', ['as' => 'ajax.crate.type.primary.save']);

    $rt->get('export/excel', 'CommonController::exportExcel');
    $rt->get('export/pdf', 'CommonController::exportPDF');
});

$routes->addRedirect('/', 'login');

$routes->set404Override(static function () {
    return view('404');
});