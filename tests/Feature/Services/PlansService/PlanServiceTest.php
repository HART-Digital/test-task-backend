<?php

namespace Tests\Feature\Services\PlansService;

use App\Services\PlansService\PlansService;
use Database\Factories\PlanFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlanFactory $planFactory;
    private PlansService $plansService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planFactory = new PlanFactory();
        $this->plansService = new PlansService();
    }

    public function testGetList()
    {
        $this->planFactory->count(16)->create();
        $list = $this->plansService->getList();

        $this->assertArrayHasKey('items', $list);
        $this->assertArrayHasKey('currentPage', $list);
        $this->assertArrayHasKey('lastPage', $list);
        $this->assertArrayHasKey('perPage', $list);
        $this->assertArrayHasKey('total', $list);

        $this->assertEquals(15, $list['perPage']);
        $this->assertCount($list['perPage'], $list['items']);
    }

    public function testGetPlan()
    {
        $plan = $this->planFactory->create();
        $data = $this->plansService->getPlan($plan->id);

        $keys = [
            'id',
            'links',
            'paths',
            'steps',
            'status',
            'public',
        ];
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        $linksKeys = [
            'public',
            'model',
            'panorama',
            'neural',
        ];
        foreach ($linksKeys as $key) {
            $this->assertArrayHasKey($key, $data['links']);
        }

        $linksNeuralKeys = [
            'editor',
            'svg',
        ];
        foreach ($linksNeuralKeys as $key) {
            $this->assertArrayHasKey($key, $data['links']['neural']);
        }

        $pathsKeys = [
            'plan',
            'mask',
            'furniture',
            'neural',
            'unreal',
            'logs',
        ];
        foreach ($pathsKeys as $key) {
            $this->assertArrayHasKey($key, $data['paths']);
        }

        $this->assertIsBool($data['public']);
    }

    public function testMakePublic()
    {
        $plan = $this->planFactory->create(
            [
                'public' => false,
            ]
        );

        $this->plansService->makePublic($plan->id);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $plan->id,
                'public' => true,
            ]
        );
    }

    public function testMakePrivate()
    {
        $plan = $this->planFactory->create(
            [
                'public' => true,
            ]
        );

        $this->plansService->makePrivate($plan->id);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $plan->id,
                'public' => false,
            ]
        );
    }

    public function testGetPublicPlan()
    {
        $plan = $this->planFactory->create(
            [
                'public' => true,

                'paths' => [
                    'plan' => 'path/to/plan',
                    'neural' => 'path/to/neural',
                    'unreal' => [
                        'path/to/unreal/1',
                        'path/to/unreal/2',
                        'path/to/unreal/3',
                        'path/to/unreal/4',
                        'path/to/unreal/5',
                        'path/to/unreal/6',
                        'path/to/unreal/7',
                        'path/to/unreal/8',
                    ],
                ],

                'hidden_paths' => [
                    'path/to/unreal/4',
                    'path/to/unreal/7',
                    'path/to/unreal/8',
                ],
            ]
        );

        $publicUnrealPaths = array_map(
            fn($p) => \Storage::url($p),
            [
                'path/to/unreal/1',
                'path/to/unreal/2',
                'path/to/unreal/3',
                'path/to/unreal/5',
                'path/to/unreal/6',
            ]
        );

        $publicPlan = $this->plansService->getPublicPlan($plan->id);

        $keys = [
            'id',
            'paths',
            'links',
            'hasModel',
            'hasPanorama',
        ];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $publicPlan);
        }

        $this->assertEquals($publicUnrealPaths, $publicPlan['paths']['unreal']);

        $this->assertArrayHasKey('model', $publicPlan['links']);
        $this->assertArrayHasKey('panorama', $publicPlan['links']);
    }

    public function testMakePathsHidden()
    {
        $paths = [
            'path/to/unreal/test1',
            'path/to/unreal/test2',
            'path/to/unreal/test3',
        ];

        $plan = $this->planFactory->create(
            [
                'paths' => [
                    'plan' => 'path/to/plan',
                    'neural' => 'path/to/neural',
                    'unreal' => [
                        'path/to/unreal/1',
                        'path/to/unreal/2',
                        'path/to/unreal/3',
                        'path/to/unreal/4',
                        'path/to/unreal/5',
                        'path/to/unreal/6',
                        'path/to/unreal/7',
                        'path/to/unreal/8',
                        ...$paths,
                    ],
                ],
                'hidden_paths' => [],
            ]
        );

        $this->plansService->makePathsHidden($plan->id, $paths);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $plan->id,
                'hidden_paths' => json_encode($paths),
            ]
        );
    }

    public function testMakePathsVisible()
    {
        $paths = [
            'path/to/unreal/test1',
            'path/to/unreal/test2',
            'path/to/unreal/test3',
        ];

        $plan = $this->planFactory->create(
            [
                'paths' => [
                    'plan' => 'path/to/plan',
                    'neural' => 'path/to/neural',
                    'unreal' => [
                        'path/to/unreal/1',
                        'path/to/unreal/2',
                        'path/to/unreal/3',
                        'path/to/unreal/4',
                        'path/to/unreal/5',
                        'path/to/unreal/6',
                        'path/to/unreal/7',
                        'path/to/unreal/8',
                        ...$paths,
                    ],
                ],
                'hidden_paths' => [
                    'path/to/unreal/1',
                    ...$paths,
                ],
            ]
        );

        $this->plansService->makePathsVisible($plan->id, $paths);

        $this->assertDatabaseHas(
            'plans',
            [
                'id' => $plan->id,
                'hidden_paths' => json_encode(
                    [
                        'path/to/unreal/1',
                    ]
                ),
            ]
        );
    }

    public function testMakePathFromUrl()
    {
        $path = 'path/to/unreal/test';

        $plan = $this->planFactory->create(
            [
                'paths' => [
                    'plan' => 'path/to/plan',
                    'neural' => 'path/to/neural',
                    'unreal' => [
                        $path,
                    ],
                ],
            ]
        );

        $url = \Storage::url($path);
        $decodedPath = $this->plansService->makePathFromUrl($plan->id, $url);

        $this->assertEquals($decodedPath, $path);
    }

    public function testMakePathsFromUrls()
    {
        $paths = ['path/to/unreal/test'];

        $plan = $this->planFactory->create(
            [
                'paths' => [
                    'plan' => 'path/to/plan',
                    'neural' => 'path/to/neural',
                    'unreal' => $paths,
                ],
            ]
        );

        $urls = array_map(fn($p) => \Storage::url($p), $paths);

        $decodedPaths = $this->plansService->makePathsFromUrls($plan->id, $urls);

        $this->assertEquals($decodedPaths, $paths);
    }

    public function testGetDataForEditor()
    {
        $plan = $this->planFactory->create(
            [
                'paths' => [
                    'plan' => 'path/to/plan',
                    'furniture' => 'path/to/furniture',
                    'neural' => null,
                ],
            ]
        );

        $editorData = $this->plansService->getDataForEditor($plan->id);

        $this->assertEquals(
            [
                'plan' => 'path/to/plan',
                'furniture' => 'path/to/furniture',
            ],
            $editorData
        );
    }

    public function testGetPlanMeta()
    {
        $plan = $this->planFactory->create();

        $meta = $this->plansService->getMeta($plan->id);

        $keys = [
            'createdAt',
            'updatedAt',
            'options',
            'additional',
        ];

        $this->assertEmpty(array_diff($keys, array_keys($meta)));
        $this->assertArrayNotHasKey('steps', $meta['options']);
        $this->assertArrayNotHasKey('currentStep', $meta['additional']);
    }
}
