<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;

class FilterRepository
{
    public static function processFilterData($filter)
    {
        $dataFilters = [];
        $formatted_projects = [];
        $formatted_subProjects = [];
        $formatted_offers = [];
        $formatted_products = [];
        $formatted_initiators = [];
        $formatted_tags = [];

        if ($filter['project']) {
            $projectsIds = is_array($filter['project']) ? $filter['project'] : explode(',', $filter['project']);

            $projects = Project::whereIn('id', $projectsIds)->where('parent_id', 0)->get();
            foreach ($projects as $key => $project) {
                $formatted_projects[] = ['id' => $project->id, 'text' => $project->name];
            }
            $dataFilters['dataProject'] = json_encode($formatted_projects);
            $dataFilters['dataProjectIds'] = $filter['project'];
        }

        if (isset($filter['sub_project'])) {
            $subProjectsIds = is_array($filter['sub_project']) ? $filter['sub_project'] : explode(',', $filter['sub_project']);
            $subProjects = Project::whereIn('id', $subProjectsIds)->where('parent_id', '!=', 0)->get();
            foreach ($subProjects as $key => $subProject) {
                $formatted_subProjects[] = ['id' => $subProject->id, 'text' => $subProject->name];
            }
            $dataFilters['dataSubProject'] = json_encode($formatted_subProjects);
            $dataFilters['dataSubProjectIds'] = $filter['sub_project'];
        }

        if (isset($filter['division'])) {
            $divisionsIds = is_array($filter['division']) ? $filter['division'] : explode(',', $filter['division']);
            $divisions = Project::whereIn('id', $divisionsIds)->division()->get();
            foreach ($divisions as $key => $division) {
                $formatted_subProjects[] = ['id' => $division->id, 'text' => $division->name];
            }
            $dataFilters['dataDivisions'] = json_encode($formatted_subProjects);
            $dataFilters['dataDivisionIds'] = $filter['division'];
        }

        if (isset($filter['offers'])) {
            $offersIds = is_array($filter['offers']) ? $filter['offers'] : explode(',', $filter['offers']);

            $offers = Offer::whereIn('id', $offersIds)->get();
            foreach ($offers as $key => $offer) {
                $formatted_offers[] = ['id' => $offer->id, 'text' => $offer->name];
            }
            $dataFilters['dataOffers'] = json_encode($formatted_offers);
            $dataFilters['dataOffersIds'] = json_encode($filter['offers']);
        }

        if (isset($filter['product'])) {
            $productsIds = is_array($filter['product']) ? $filter['product'] : explode(',', $filter['product']);

            $products = Product::whereIn('id', $productsIds)->get();
            foreach ($products as $key => $product) {
                $formatted_products[] = ['id' => $product->id, 'text' => $product->title];
            }

            $dataFilters['dataProducts'] = json_encode($formatted_products);
            $dataFilters['dataProductsIds'] = json_encode($filter['product']);
        }

        if (isset($filter['initiator'])) {
            $usersIds = is_array($filter['initiator']) ? $filter['initiator'] : explode(',', $filter['initiator']);
            $users = User::whereIn('id', $usersIds)->get();
            foreach ($users as $user) {
                $formatted_initiators[] = ['id' => $user->id, 'text' => $user->surname . ' ' . $user->name];
            }
            $dataFilters['dataInitiators'] =  json_encode($formatted_initiators);
            $dataFilters['dataInitiatorsIds'] =  json_encode($filter['initiator']);
        }

        $tagsArray = [
            Tag::TAG_SOURCE, Tag::TAG_CAMPAIGN, Tag::TAG_CONTENT, Tag::TAG_MEDIUM, Tag::TAG_TERM
        ];

        foreach ($tagsArray as $tag){
            if (isset($filter[$tag])) {
                $tagsIds = is_array($filter[$tag]) ? $filter[$tag] : explode(',', $filter[$tag]);

                $tags = Tag::whereIn('id', $tagsIds)->get();

                foreach ($tags as $value) {
                    $formatted_tags[] = ['id' => $value->id, 'text' => $value->value];
                }
                $dataFilters[$tag] =  json_encode($formatted_tags);
                $dataFilters['ids_'. $tag] =  json_encode($filter[$tag]);
            }
        }

        return $dataFilters;
    }
}