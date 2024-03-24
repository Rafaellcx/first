<?php

namespace Tests\Unit;
use App\Http\Helpers\JsonFormat;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Services\UserService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mockery;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;
use Faker\Factory as Faker;

# php artisan test --filter=UserServiceTest --env=testing
class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    # php artisan test --filter=UserServiceTest::test_index_returns_user_resource_collection --env=testing
    public function test_index_returns_user_resource_collection()
    {
        // Create a mock UserRepositoryContract instance
        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        // Define expected behavior for the UserRepositoryContract's index method
        $userRepositoryMock->expects('index')
            ->andReturns([]);

        // Create a UserService instance by injecting the mock UserRepository
        $userService = new UserService($userRepositoryMock);

        // Call the UserService index function
        $result = $userService->index();

        // Check if the return is an instance of AnonymousResourceCollection
        $this->assertInstanceOf(AnonymousResourceCollection::class, $result);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    # php artisan test --filter=UserServiceTest::test_save_successfully --env=testing
    public function test_save_successfully()
    {
        $faker = Faker::create();

        $password = bcrypt($faker->month . $faker->randomNumber(5));

        // Simulate input data to save a user
        $userData = [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        // Creates a fake UserRepositoryContract instance
        $userRepository = $this->createMock(UserRepositoryContract::class);

        // Configure the mock to wait for a call to the save method with user data
        $userRepository->expects($this->once())
            ->method('save')
            ->with($userData);

        // Create a UserService instance using the UserRepository Contract mock
        $userService = new UserService($userRepository);

        // Calls the UserService's save function with the simulated data
        $response = $userService->save($userData);

        // Checks if the response is an instance of JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Checks whether the response contains a success message
        $this->assertEquals(JsonFormat::success('User was saved successfully.', [], 201)->getContent(), $response->getContent());
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    # php artisan test --filter=UserServiceTest::test_save_unsuccessfully_throws_an_error --env=testing
    public function test_save_unsuccessfully_throws_an_error()
    {
        // Create a mock instance of UserRepositoryContract
        $mockUserRepository = $this->createMock(UserRepositoryContract::class);

        // Set up expectations for the mock: expects the save method to be called once
        // and throws an exception with the message 'Save failed'
        $mockUserRepository->expects($this->once())
            ->method('save')
            ->willThrowException(new Exception('Save failed'));

        // Create an instance of UserService injecting the mock UserRepositoryContract
        $userService = new UserService($mockUserRepository);

        // Call the save method of the UserService with an empty array (simulated data)
        $response = $userService->save([]);

        // Define the expected response in case of an error
        $expectedResponse = [
            'status' => 'failed',
            'message' => 'Ops, User not saved.',
            'data' => []
        ];

        // Assert that the response data matches the expected response
        $this->assertEquals($expectedResponse, (array)$response->getData());

        // Assert that the status code of the response is HTTP_BAD_REQUEST
        $this->assertEquals(ResponseAlias::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
