<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClusterResults extends Model
{
    protected $table = 'cluster_results';

    protected $guarded = [];

    public array $clusters;

    public function setClusters($default_result)
    {
        $this->clusters = Cluster::unpackCluster($default_result);
    }

    public function parseTree($array): string
    {
        $html = '';
        foreach ($array as $mainPhrase => $items) {
            $id = str_replace(' ', '_', $mainPhrase);
            $html .= '<li id="' . $id . '" class="cluster-block">' .
                ClusterResults::generateHeader($mainPhrase) .
                ClusterResults::generateOl($items, $mainPhrase) .
                '</li>';
        }

        return $html;
    }

    public static function generateHeader($mainPhrase): string
    {
        return '
            <div class="card-header">
                <div class="d-flex justify-content-between text-white">
                    <span class="w-50">' . $mainPhrase . '</span>
                    <span></span>
                    <span></span>
                    <div class="btn-group btn-group-toggle w-75" style="display: none">
                         <input type="text" value="' . $mainPhrase . '" data-target="' . $mainPhrase . '" class="form form-control group-name-input">
                         <button class="btn btn-secondary edit-group-name">' . __('Change') . '</button>
                    </div>
                    <div class="d-flex justify-content-between">
                       <span class="__helper-link ui_tooltip_w">
                           <i class="fa fa-filter mr-2" style="color: white"
                              data-action="desc"></i>
                           <span class="ui_tooltip __bottom">
                               <span class="ui_tooltip_content">
                                   ' . __("Sort") . '
                               </span>
                           </span>
                       </span>

                        <span class="__helper-link ui_tooltip_w">
                            <i data-action="hide" class="fa fa-eye mr-2" style="color: white;"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content">
                                     ' . __("Hide a group") . '
                                 </span>
                             </span>
                         </span>
                        <span class="__helper-link ui_tooltip_w">
                           <i class="fa fa-edit change-group-name mr-2" style="color: white; padding-top: 5px;"></i>
                           <span class="ui_tooltip __bottom">
                               <span class="ui_tooltip_content">' . __("Change the name") . '</span>
                           </span>
                        </span>
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-check select-group mr-2" style="color: white; padding-top: 5px;"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content">' . __("Select a group to move phrases quickly") . '</span>
                            </span>
                        </span>
                        <span class="__helper-link ui_tooltip_w">
                            <i class="fa fa-arrow-right move-group mr-2" style="color: white; padding-top: 5px;"></i>
                            <span class="ui_tooltip __bottom">
                                <span class="ui_tooltip_content">' . __("Move the entire group") . '</span>
                            </span>
                        </span>
                    </div>
                    <span class="__helper-link ui_tooltip_w hide set-relevance-link">
                       <button data-toggle="modal" data-target="#setRelevanceLink" class="btn btn-secondary" style="border-radius: 0px !important;">
                           <i class="fa fa-save" style="color: white;"></i>
                       </button>
                       <span class="ui_tooltip __bottom">
                           <span class="ui_tooltip_content">' . __("Select one url for the entire group of phrases") . '</span>
                       </span>
                    </span>
                </div>
            </div>';
    }

    public function generateOl($items, $mainPhrase): string
    {
        $boolean = false;
        if ($mainPhrase == 'Нераспределённые слова' || $mainPhrase == 'Unallocated words' || $mainPhrase == 'нераспределённые слова') {
            $boolean = true;
        }

        $ol = '<ol id="' . Str::random(5) . '" class="list-group list-group-flush show">';

        foreach ($items as $phrase) {
            if (is_array($phrase)) {
                $ol .= $this->parseTree($phrase);
            } else {
                if ($boolean) {
                    $mainPhrase = $phrase;
                }

                $targetElement = $this->clusters[$mainPhrase][$phrase] ?? $this->searchElement($phrase, true);

                $ol .= '<div data-target="' . $phrase . '" data-action="' . $mainPhrase . '" class="list-group-item">
                           <div class="d-flex justify-content-between align-items-center">
                               <div class="phrase-for-color">' . $phrase . '</div>
                               <span class="relevance-link hide">' . Cluster::getRelevanceLink($targetElement) . '</span>
                               <div class="hide">' . implode("\n", array_keys($targetElement)) . '</div>
                               <div>
                                    <span class="__helper-link ui_tooltip_w frequency">
                                        <span>' . ClusterResults::getVisibilityCounter($targetElement['based']) . '</span> /
                                        <span>' . ClusterResults::getVisibilityCounter($targetElement['phrased']) . '</span> /
                                        <span>' . ClusterResults::getVisibilityCounter($targetElement['target']) . '</span>
                                        <span class="ui_tooltip __bottom">
                                            <span class="ui_tooltip_content">
                                                <span>' . __("Base") . '</span> /
                                                <span>' . __("Phrasal") . '</span> /
                                                <span>' . __("Target") . '</span>
                                            </span>
                                        </span>
                                    </span>
                               </div>
                               <div class="btn-group">
                                   <i data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="fa fa-ellipsis mr-2"></i>
                                   <div class="dropdown-menu">
                                      <button data-toggle="modal" data-target="#exampleModal" data-action="' . $phrase . '" class="dropdown-item add-to-another">
                                         ' . __("Add a phrase to another cluster") . '
                                      </button>
                                      <button data-toggle="modal" class="dropdown-item select-for-analyse">
                                          ' . __("Select phase for analyse") . '
                                      </button>
                                      <button data-toggle="modal" class="dropdown-item color-phrases">
                                          ' . __("Highlight similar phrases") . '
                                      </button>
                                      <button data-toggle="modal" class="dropdown-item set-default-colors">
                                          ' . __("Cancel selection") . '
                                      </button>
                                   </div>
                                   <i data-target="' . $phrase . '" class="fa fa-arrow-right move-phrase"></i>
                               </div>
                          </div>
                     </div>';
            }
        }
        $ol .= '</ol>';

        return $ol;
    }

    public function searchElement($ph, $similarities = false): array
    {
        $item = [];
        foreach ($this->clusters as $mainPhrase => $items) {
            if (array_key_exists($ph, $items)) {
                $item = $this->clusters[$mainPhrase][$ph];
                break;
            }
        }

        if ($similarities) {
            return $item['similarities'] ?? [];
        }

        return $item;
    }

    private static function getVisibilityCounter($item)
    {
        if (isset($item['number'])) {
            return $item['number'];
        }

        return $item;
    }
}
