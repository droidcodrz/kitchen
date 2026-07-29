<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $marrionClient = Client::where('name', 'LIKE', '%Marrion%')->first();
        $cloudKitchenClient = Client::where('name', 'LIKE', '%Cloud Kitchen%')->first();
        $grandPalaceClient = Client::where('name', 'LIKE', '%Grand Palace%')->first();
        $freshFoodsClient = Client::where('name', 'LIKE', '%Fresh Foods%')->first();
        $restaurantClient = Client::where('name', 'LIKE', '%Restaurant%')->first();

        $projectManager = User::where('email', 'arjun.mehta@company.com')->first();
        $projectManager2 = User::where('email', 'kavita.desai@company.com')->first();

        $teamAlpha = Team::where('slug', 'team-alpha')->first();
        $teamBeta = Team::where('slug', 'team-beta')->first();
        $teamGamma = Team::where('slug', 'team-gamma')->first();

        $halalProduct = Product::where('slug', 'halal-kitchen-setup-marinari')->first();
        $cloudKitchenProduct = Product::where('slug', 'cloud-kitchen-equipment-package')->first();

        $projects = [
            [
                'order_no' => 'ORD-48921',
                'name' => 'Halal Kitchen Setup - Marinari',
                'slug' => 'halal-kitchen-setup-marinari-48921',
                'client_id' => $marrionClient->id,
                'project_manager_id' => $projectManager->id,
                'status' => 'in_production',
                'proposal_signed_date' => '2026-02-10',
                'delivery_date' => '2026-03-15',
                'production_deadline' => '2026-03-10',
                'actual_delivery_date' => null,
                'description' => 'Complete Halal-certified kitchen installation for Marrion Hotels flagship property',
                'notes' => 'Project progressing well. Client requested additional equipment modifications.',
                'product_ids' => [$halalProduct->id],
                'team_ids' => [$teamAlpha->id, $teamBeta->id],
            ],
            [
                'order_no' => 'ORD-48922',
                'name' => 'Cloud Kitchen Equipment',
                'slug' => 'cloud-kitchen-equipment-48922',
                'client_id' => $cloudKitchenClient->id,
                'project_manager_id' => $projectManager->id,
                'status' => 'delayed',
                'proposal_signed_date' => '2026-02-15',
                'delivery_date' => '2026-02-28',
                'production_deadline' => '2026-02-25',
                'actual_delivery_date' => null,
                'description' => 'Full equipment package for cloud kitchen operations',
                'notes' => 'Delayed due to compressor supply issues. Recovery plan in place.',
                'product_ids' => [$cloudKitchenProduct->id],
                'team_ids' => [$teamAlpha->id, $teamGamma->id],
            ],
            [
                'order_no' => 'ORD-48821',
                'name' => 'Grand Palace Refrigeration',
                'slug' => 'grand-palace-refrigeration-48821',
                'client_id' => $grandPalaceClient->id,
                'project_manager_id' => $projectManager2->id,
                'status' => 'delivered',
                'proposal_signed_date' => '2026-01-05',
                'delivery_date' => '2026-02-20',
                'production_deadline' => '2026-02-15',
                'actual_delivery_date' => '2026-02-18',
                'description' => 'Industrial refrigeration system for Grand Palace Resort',
                'notes' => 'Project completed successfully. Client very satisfied.',
                'product_ids' => Product::where('category_id', 2)->limit(3)->pluck('id')->toArray(),
                'team_ids' => [$teamBeta->id],
            ],
            [
                'order_no' => 'ORD-48923',
                'name' => 'Fresh Foods Processing Line',
                'slug' => 'fresh-foods-processing-line-48923',
                'client_id' => $freshFoodsClient->id,
                'project_manager_id' => $projectManager2->id,
                'status' => 'confirmed',
                'proposal_signed_date' => '2026-02-28',
                'delivery_date' => '2026-03-25',
                'production_deadline' => '2026-03-20',
                'actual_delivery_date' => null,
                'description' => 'Commercial food processing equipment installation',
                'notes' => 'Design approved. Production starting next week.',
                'product_ids' => Product::where('category_id', 4)->limit(2)->pluck('id')->toArray(),
                'team_ids' => [$teamGamma->id],
            ],
            [
                'order_no' => 'ORD-48820',
                'name' => 'Marrion Hotel Kitchen Expansion',
                'slug' => 'marrion-hotel-kitchen-expansion-48820',
                'client_id' => $marrionClient->id,
                'project_manager_id' => $projectManager->id,
                'status' => 'in_production',
                'proposal_signed_date' => '2026-02-05',
                'delivery_date' => '2026-03-10',
                'production_deadline' => '2026-03-05',
                'actual_delivery_date' => null,
                'description' => 'Kitchen expansion project for Marrion Hotels secondary location',
                'notes' => 'On schedule. Installation phase starting next week.',
                'product_ids' => Product::limit(4)->pluck('id')->toArray(),
                'team_ids' => [$teamAlpha->id, $teamBeta->id, $teamGamma->id],
            ],
            [
                'order_no' => 'ORD-48819',
                'name' => 'Restaurant Chain Standardization',
                'slug' => 'restaurant-chain-standardization-48819',
                'client_id' => $restaurantClient->id,
                'project_manager_id' => $projectManager->id,
                'status' => 'draft',
                'proposal_signed_date' => null,
                'delivery_date' => '2026-04-15',
                'production_deadline' => '2026-04-10',
                'actual_delivery_date' => null,
                'description' => 'Standard kitchen equipment rollout for restaurant chain',
                'notes' => 'Initial planning phase. Site surveys scheduled.',
                'product_ids' => Product::limit(5)->pluck('id')->toArray(),
                'team_ids' => [$teamBeta->id],
            ],
        ];

        foreach ($projects as $projectData) {
            $productIds = $projectData['product_ids'];
            $teamIds = $projectData['team_ids'];
            unset($projectData['product_ids'], $projectData['team_ids']);

            $project = Project::create($projectData);

            // Attach products
            if (!empty($productIds)) {
                $project->products()->attach($productIds);
            }

            // Attach teams
            if (!empty($teamIds)) {
                $project->teams()->attach($teamIds);
            }

            // Attach team members as project members
            foreach ($teamIds as $teamId) {
                $team = Team::find($teamId);
                if ($team) {
                    $memberIds = $team->users()->pluck('users.id')->toArray();
                    if (!empty($memberIds)) {
                        $project->members()->attach($memberIds);
                    }
                }
            }
        }
    }
}
