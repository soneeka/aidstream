@extends('app')

@section('title', trans('title.name'))

@section('content')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
                @include('includes.response')
                <div class="element-panel-heading">
                    <div>
                        <span>{{ ($organizations) ? 'Edit Partner Organization' : 'Add a new Partner Organization' }}</span>
                    </div>
                    {{--<div>@lang('element.name')--}}
                    {{--<div class="panel-action-btn">--}}
                    {{--<a href="{{route('organization.show', $id)}}" class="btn btn-primary btn-view-it">@lang('global.view_organisation_data')--}}
                    {{--</a>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="create-form">
                                <div v-cloak class="create-form" id="participatingContainer"
                                     data-organization="{{json_encode($organizations)}}" data-route="{{$formRoute}}">
                                    {{Form::open()}}
                                    <participating-org v-for="(organisation,index) in organisations"
                                                       v-on:search="setCurrentOrganization(index,$event)"
                                                       :organisation="organisation"
                                                       v-on:display="displayModal($event)"
                                                       :display_error="display_error">
                                    </participating-org>
                                    <modal v-show="showModal" v-on:close="closeModal"
                                           :organisation="currentOrganisation"
                                           :registrar_list="registrarList"></modal>
                                    <button class="btn btn-submit btn-form" type="submit" v-on:click.prevent="onSubmit">
                                        Save
                                    </button>
                                    <a class="btn btn-cancel" href="{{route('organization.index', $id)}}">Cancel</a>
                                </div>
                                {{Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
                {{--                @include('includes.menu_org')--}}
            </div>
        </div>
    </div>

    <div id="participating-form" class="hidden">
        <div class="collection_form has_add_more">
            <div class="form-group">
                <div class="form-group" v-bind:class="{'has-error': (organisation.type == '' && display_error)}">
                    {{Form::label('Organisation Type',trans('elementForm.organisation_type'),['class' => 'control-label'])}}
                    {{Form::select('type',$organizationTypes, null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.type', 'v-on:change'=>'onchange($event)', 'placeholder' => 'Please select the following options.','v-bind:readonly' => "disable_options"])}}
                    <div v-if="(organisation.type == '' && display_error)" class="text-danger">Organisation Type is
                        required.
                    </div>
                </div>

                <div class="form-group"
                     v-bind:class="{'has-error': (organisation.country == '' && display_error), 'disabled': disable_options}">
                    {{Form::label('country','Country the organization is based in',['class' => 'control-label'])}}
                    {{Form::select('country',$countries, null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.country', 'v-on:change'=>'onchange($event)', 'placeholder' => 'Please select the following options.','v-bind:readonly' => "disable_options"])}}
                    <div v-if="(organisation.country == '' && display_error)" class="text-danger">Country is required.
                    </div>
                </div>
                <div class="form-group"
                     v-bind:class="{'has-error': (organisation.name[0]['narrative'] == '' && display_error)}">
                    {{Form::label('Organization',trans('elementForm.organisation'),['class' => 'control-label'])}}
                    {{Form::text('organization',null,['class' => 'form-control ignore_change','v-bind:value' => "organisation.name[0]['narrative']",'@focus' => 'displaySuggestion($event)', '@keydown.tab'=> 'hideSuggestion','@blur'=>'hide($event)','autocomplete' => 'off', 'readonly' => true])}}

                    <div v-if="(organisation.name[0]['narrative'] == '' && display_error)" class="text-danger">
                        Organisation Name is required.
                    </div>

                    <div v-if="display_org_list" class="publisher-wrap">
                        <ul class="filter-publishers">
                            <li>
                                <div class="search-publishers">
                                    <input type="search" :value="keyword" placeholder="Filter by organisation name..." @keyup ='search($event)'>
                                </div>
                            </li>
                        </ul>

                        <ul v-if="suggestions.length > 0" class="found-publishers filter-publishers scroll-list">
                            <li class="publisher-description"><p>Choose an organisation from below</p></li>
                            <li v-for="(publisher, index) in suggestions">
                                <a href="#" v-on:click="selected($event)" v-bind:selectedSuggestion="index">
                                    <strong v-bind:selectedSuggestion="index">@{{publisher.identifier}} @{{publisher.name}}</strong>
                                    <div class="partners">
                                        <div class="pull-left">
                                            <span v-bind:selectedSuggestion="index">Type: @{{publisher.type}}</span>
                                        </div>
                                        <div class="pull-right">
                                            <span class="suggest-edit">Suggest Edit</span>
                                        </div>
                                    </div>
                                </a>

                            </li>
                            <li><p>The above list is pulled from IATI Registry publisher's list.</p></li>
                        </ul>
                        <ul v-if="display_org_finder" class="not-found-publisher">
                            <li class="publisher-description"><p>It seems there's no matching organisation in IATI Registry of publishers. You may do one
                                    of the following at this point.</p></li>
                            <li class="contact-org" id="orgFinder">
                                <a href="#">
                                    <h3 class="contact-heading">Contact Organisation</h3>
                                    <p>Send them a message letting them know about this.</p>
                                </a>
                            </li>
                            <li class="or">Or</li>
                            <li id="orgFinder">
                                <a href="#" @click="display()">
                                <h3 class="contact-heading">Use Organization Finder <span> (org-id.guide)</span></h3>
                                <p>Use our organization finder helper to get a new identifier for this.</p>
                                <p><span class="caution">Caution:</span> Please beware that this can be a long and
                                    tedious process. It may be the case that you will not
                                    find the organization even with this. In this case, leave the identifier field blank
                                    and just mention organisation name only.</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="form-group organisation-identifier">
                    {{Form::label('organisation_identifier','Organisation Identifier:',['class' => 'control-label'])}}
                    @{{organisation.identifier}}
                </div>
            </div>
        </div>
    </div>

    <div class="hidden" id="modalComponent">
        <div class="modal fade org-modal" id="myModal" role="dialog">
            <div class="modal-dialog ">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" @click="close">&times;</button>
                        <h4 class="modal-title">Add from org-id.guide</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            {{Form::label('Organisation Type',trans('elementForm.organisation_type'),['class' => 'control-label'])}}
                            {{Form::select('type',$organizationTypes, null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.type', 'placeholder' => 'Please select the following options.', 'v-on:change' => 'getRegistrars($event)'])}}
                        </div>

                        <div class="form-group">
                            {{Form::label('country','Country the organization is based in',['class' => 'control-label'])}}
                            {{Form::select('country',$countries, null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.country', 'placeholder' => 'Please select the following options.','v-on:change' => 'getRegistrars($event)'])}}
                        </div>
                        <div class="suggestions" v-if="display_registrar_list">
                            <h3>Please choose a list from below</h3>
                            <div class="lists scroll-list">
                                <ul>
                                    <li v-for="(list,index) in registrar_list[0]">
                                        <div class="register-list">
                                            <label>
                                                <input type="radio" name="registrar" v-on:change="displayForm($event)"
                                                       v-bind:value="list['code']"/>
                                                <span>@{{ list['name']['en'] }}
                                                    <strong>(@{{ list['code'] }})</strong></span>
                                            </label>
                                        </div>
                                        <div class="score-block"><span>Quality Score: <strong>@{{ list['quality'] }}</strong></span><span><a
                                                        v-bind:href="list.url" target="_blank">View this list →</a></span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-if="display_org_info_form">
                                <div class="form-group">
                                    {{Form::label('Organisation Name',trans('elementForm.organisation_name'),['class' => 'control-label'])}}
                                    {{Form::text('name', null,['class' => 'form-control ignore_change', "v-bind:value" => "organisation.name[0]['narrative']", "@blur" => 'updateOrgName($event)'])}}
                                </div>

                                <div class="form-group">
                                    {{Form::label('Identifier','Organisation Registration Number',['class' => 'control-label'])}}
                                    {{Form::text('identifier', null,['class' => 'form-control ignore_change', 'v-bind:value' => 'organisation.identifier', "@blur" => 'updateOrgIdentifier($event)'])}}
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-form" type="button" @click="close">Use this organisation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="reset-form-option">
                            <a @click="resetForm">Reset form</a>
                            <p>Reset the above form to start again.</p>
                        </div>
                        {{--<button type="button" class="btn btn-default" data-dismiss="modal" @click="close">Close</button>--}}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://unpkg.com/vue"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
      Vue.component('participating-org', {
        template: '#participating-form',
        data: function () {
          return {
            display_org_finder: false,
            disable_options: false,
            display_org_list: false,
            suggestions: [],
            searching: false,
            keyword: ''
          }
        },
        updated: function () {
          $('.scroll-list').jScrollPane({autoReinitialise: true});
        },
        mounted: function () {
          if (this.organisation.is_publisher) {
            this.disable_options = true;
          }
        },
        props: ['organisation', 'display_error'],
        methods: {
          displaySuggestion: function (event) {
            this.display_org_list = true;
            this.keyword = '';
          },
          search: function (event) {
            var self = this;
            this.keyword = event.target.value;
            if (event.target.value.trim().length > 3) {
              if (!self.searching) {
                self.searching = true;
                this.disable_options = false;
//                self.organisation['name'][0]['narrative'] = '';
//                self.organisation['identifier'] = '';
                this.suggestions.splice(0, this.suggestions.length);
                setTimeout(function () {
                  axios.get('/findpublisher?name=' + event.target.value + '&identifier=' + event.target.value)
                    .then(function (response) {
                      self.searching = false;
                      response.data.forEach(function (publisher) {
                        publisher.is_publisher = true;
                        self.suggestions.push(publisher);
                        self.display_org_finder = false;
                      });
                    })
                    .catch(function (error) {
                      self.organisation['is_publisher'] = false;
                      self.suggestions.splice(0, self.suggestions.length);
                      self.display_org_finder = true;
                      self.searching = false;
                    });
                }, 1000);
              }
            } else {
              this.suggestions.splice(0, this.suggestions.length);
              this.display_org_finder = false;
            }
            this.$emit('search', this.index);
          },
          hideSuggestion: function () {
            this.display_org_finder = false;
            this.display_org_list = false;
            this.suggestions.splice(0, this.suggestions.length);
          },
          onchange: function (event) {
            this.organisation[event.target.name] = event.target.value;
          },
          display: function () {
            this.display_org_finder = false;
            this.display_org_list = false;
            var country = this.organisation['country'];
            var self = this;
            if (country != "") {
              axios.get('/findorg?country=' + country)
                .then(function (response) {
                  self.$emit('display', response.data);
                });
            }
            self.$emit('display', []);
          },
          selected: function (event) {
            var selectedIndex = event.target.getAttribute('selectedSuggestion');
            this.organisation['type'] = this.suggestions[selectedIndex]['type'];
            this.organisation['is_publisher'] = this.suggestions[selectedIndex]['is_publisher'];
            this.organisation['identifier'] = this.suggestions[selectedIndex]['identifier'];
            this.organisation['name'][0]['narrative'] = this.suggestions[selectedIndex]['name'];
            this.organisation['country'] = this.suggestions[selectedIndex]['country'];
            this.organisation['name'][0]['language'] = 'en';
            this.disable_options = true;
            this.display_org_list = false;
            this.suggestions.splice(0, this.suggestions.length);
          },
          hide: function (event) {
            if (!event.relatedTarget) {
              this.display_org_finder = false;
              this.display_org_list = false;
              this.suggestions.splice(0, this.suggestions.length);
            }
          }
        }
      });

      Vue.component('modal', {
        template: '#modalComponent',
        props: ['organisation', 'registrar_list'],
        data: function () {
          return {
            display_org_info_form: false,
            selectedRegistrar: '',
            display_registrar_list: false
          }
        },
        beforeUpdate: function () {
          $('.scroll-list').jScrollPane({autoReinitialise: true});
          if (this.registrar_list[0] != undefined) {
            if (this.registrar_list[0].length != 0) {
              this.display_registrar_list = true;
            }
          }
        },
        methods: {
          close: function () {
            this.$emit('close', false);
          },
          displayForm: function (event) {
            this.selectedRegistrar = event.target.getAttribute('value');
            this.display_org_info_form = true;
          },
          getRegistrars: function (event) {
            var self = this;
            this.organisation[event.target.name] = event.target.value;
            axios.get('/findorg?country=' + event.target.value)
              .then(function (response) {
                self.registrar_list.splice(0, self.registrar_list.length);
                self.registrar_list.push(response.data);
              });
          },
          updateOrgName: function (event) {
            this.organisation['name'][0]['narrative'] = event.target.value;
          },
          updateOrgIdentifier: function ($event) {
            this.organisation['identifier'] = '';
            this.organisation['identifier'] = this.selectedRegistrar + '-' + event.target.value;
          },
          resetForm: function () {
            this.defaultData();
          },
          defaultData: function () {
            this.organisation['type'] = '';
            this.organisation['is_publisher'] = '';
            this.organisation['identifier'] = '';
            this.organisation['name'][0]['narrative'] = '';
            this.organisation['country'] = '';
            this.organisation['name'][0]['language'] = '';
          }
        }
      });

      new Vue({
        el: '#participatingContainer',
        data: {
          organisations: [],
          showModal: false,
          currentOrganisation: [],
          registrarList: [],
          display_error: false
        },
        mounted: function () {
          if (JSON.parse(this.$el.getAttribute('data-organization'))) {
            this.organisations = JSON.parse(this.$el.getAttribute('data-organization'));
          } else {
            this.organisations.push({
              "identifier": "",
              "type": "",
              "country": "",
              "name": [{"narrative": ""}],
              "is_publisher": false
            });
          }
        },
        methods: {
          setCurrentOrganization: function (index) {
            this.currentOrganisation = this.organisations[index];
          },
          displayModal: function (event) {
            this.registrarList.splice(0, this.registrarList.length);
            if (event) {
              this.registrarList.push(event);
            }
            this.showModal = true;
            $('#myModal').modal();
          },
          addOrganisations: function () {
            this.organisations.push({"identifier": "", "type": "", "country": "", "name": [{"narrative": ""}]});
          },
          closeModal: function () {
            this.showModal = false;
            $('.modal-backdrop').remove();
          },
          onSubmit: function () {
            var route = this.$el.getAttribute('data-route');
            var self = this;
            if (this.isValid()) {
              axios.post(route, {organisation: self.organisations})
                .then(function (response) {
                  window.location.href = '/organization/';
                }).catch(function (error) {
              });
            }
          },
          isValid: function () {
            var organisation = this.organisations[0];

            if ((organisation.type === '') || (organisation.name[0]['narrative'] === '') || (organisation.country === '')) {
              this.display_error = true;
              return false;
            }

            this.display_error = false;
            return true;
          }
        }
      });
    </script>
@endsection

