<?php
/**
 * IndexControllerTest
 *
 * @category Tests
 * @package  Default
 */
class Users_LoginControllerTest extends ControllerTestCase
{
    /**
     * User Model
     *
     * @var Model_User
     */
    protected $_user;
    
    /**
     * Setup TestCase
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->_fixture = array('login'     => 'admin'.time(),
                                'email'     => 'testadmin@domain.com',
                                'role'      => Model_User::ROLE_ADMIN,
                                'status'    => Model_User::STATUS_ACTIVE,
                                'password'  => '123456');
        
        $manager = new Model_User_Table();
        
        $this->_user = $manager->create($this->_fixture);
        $this->_user->save();
    }
    
    /**
     * Default/Login/Index
     */
    public function testLoginIndexAction()
    {
        $this->dispatch('/login/');
        $this->assertModule('users');
        $this->assertController('login');
        $this->assertAction('index');
    }
    
    /**
     * Login page should contain Login Form
     */
    public function testLoginIndexActionShouldContainLoginForm()
    {
        $this->dispatch('/login');
        $this->assertController('login');
        $this->assertAction('index');
        $this->assertQueryCount('form#userLoginForm', 1);
    }

    /**
     * After login should be redirect ro main page
     */
    public function testValidLoginShouldGoToMainPage()
    {
        $this->_loginUser(
            $this->_fixture['login'],
            $this->_fixture['password']
        );
        $this->assertRedirectTo('/');
    }
    
    /**
     * After logout should be redirect ro main page
     */
    public function testValidLogoutShouldGoToMainPage()
    {
        $this->_loginUser(
            $this->_fixture['login'],
            $this->_fixture['password']
        );
        $this->dispatch('/logout');
        $this->assertRedirectTo('/');
    }

    /**
     * Invalid login:
     *  - show message
     *  - show form
     */
    public function testInvalidCredentialsShouldResultInRedisplayOfLoginForm()
    {
        $this->_loginUser('fakeuser', 'fakepassword');
        
        $this->assertNotRedirect();
        $this->assertQueryCount('form#userLoginForm', 1);
        $this->assertQueryCount('#messages', 1);
    }
    
    /**
     * Not valid form params:
     *  - show message
     *  - show form
     */
    public function testInvalidFormParamsShouldResultInRedisplayOfLoginForm()
    {
        $this->_loginUser("fake'user", 'fakepassword');
        $this->assertNotRedirect();
        $this->assertQueryCount('form#userLoginForm', 1);
        $this->assertQueryCount('#messages', 1);
    }

       
    /**
     * Authorisation
     *
     * @param string $username
     * @param string $password
     */
    public function _loginUser($username, $password)
    {
        $this->resetRequest()
             ->resetResponse();
        $this->request->setMethod('POST')
                      ->setPost(
                          array(
                              'login' => $username,
                              'password' => $password,
                              'rememberMe' => 1
                          )
                      );
        $this->dispatch('/login');
    }
    
    
    /**
     * tear Down
     */
    public function tearDown()
    {
        $this->_user->delete();
        
        parent::tearDown();
    }
}