<?php 

namespace Application\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

define('AVATAR_GRAVATAR',0);
define('AVATAR_TWITTER',1);
define('AVATAR_FACEBOOK',2);

/**  
 * Application\UserBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var smallint $admin
     *
     * @ORM\Column(name="admin", type="smallint")
     */
    private $admin;

    /**
     * @var bigint $facebook_id
     *
     * @ORM\Column(name="facebook_id", type="bigint", nullable=true)
     */
    private $facebook_id;

    /**
     * @var smallint $category_id
     *
     * @ORM\Column(name="category_id", type="smallint")
     */
    private $category_id;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $pass
     *
     * @ORM\Column(name="pass", type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Assert\MaxLength(6)
     */
    private $pass;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var text $body
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string $location
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var datetime $date_login
     *
     * @ORM\Column(name="date_login", type="datetime")
     */
    private $date_login;

    /**
     * @var integer $votes
     *
     * @ORM\Column(name="votes", type="integer", nullable=true)
     */
    private $votes;

    /**
     * @var integer $visits
     *
     * @ORM\Column(name="visits", type="integer", nullable=true)
     */
    private $visits;

    /**
     * @var integer $freelance
     *
     * @ORM\Column(name="freelance", type="integer", nullable=true)
     */
    private $freelance;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string $linkedin_url
     *
     * @ORM\Column(name="linkedin_url", type="string", length=255, nullable=true)
     */
    private $linkedin_url;

    /**
     * @var string $twitter_url
     *
     * @ORM\Column(name="twitter_url", type="string", length=255, nullable=true)
     */
    private $twitter_url;

    /**
     * @var string $forrst_url
     *
     * @ORM\Column(name="forrst_url", type="string", length=255, nullable=true)
     */
    private $forrst_url;

    /**
     * @var string $github_url
     *
     * @ORM\Column(name="github_url", type="string", length=255, nullable=true)
     */
    private $github_url;

    /**
     * @var string $dribbble_url
     *
     * @ORM\Column(name="dribbble_url", type="string", length=255, nullable=true)
     */
    private $dribbble_url;

    /**
     * @var string $flickr_url
     *
     * @ORM\Column(name="flickr_url", type="string", length=255, nullable=true)
     */
    private $flickr_url;

    /**
     * @var string $youtube_url
     *
     * @ORM\Column(name="youtube_url", type="string", length=255, nullable=true)
     */
    private $youtube_url;

    /**
     * @var string $stackoverflow_url
     *
     * @ORM\Column(name="stackoverflow_url", type="string", length=255, nullable=true)
     */
    private $stackoverflow_url;

    /**
     * @var string $vimeo_url
     *
     * @ORM\Column(name="vimeo_url", type="string", length=255, nullable=true)
     */
    private $vimeo_url;

    /**
     * @var string $delicious_url
     *
     * @ORM\Column(name="delicious_url", type="string", length=255, nullable=true)
     */
    private $delicious_url;

    /**
     * @var string $pinboard_url
     *
     * @ORM\Column(name="pinboard_url", type="string", length=255, nullable=true)
     */
    private $pinboard_url;

    /**
     * @var string $itunes_url
     *
     * @ORM\Column(name="itunes_url", type="string", length=255, nullable=true)
     */
    private $itunes_url;

    /**
     * @var string $android_url
     *
     * @ORM\Column(name="android_url", type="string", length=255, nullable=true)
     */
    private $android_url;

    /**
     * @var string $can_contact
     *
     * @ORM\Column(name="can_contact", type="integer", nullable=true)
     */
    private $can_contact;

    /**
     * @var string $ref_id
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $ref_id;

    /**
     * @var string $total_logins
     *
     * @ORM\Column(name="total_logins", type="integer")
     */
    private $total_logins;

    /**
     * @var string $avatar_type
     *
     * @ORM\Column(name="avatar_type", type="integer", nullable=true)
     */
    private $avatar_type;

    /**
     * @var string $ip
     *
     * @ORM\Column(name="ip", type="string", length=100)
     */
    private $ip;


    /**
     * Get id
     *
     * @return intger 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set admin
     *
     * @param smallint $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin
     *
     * @return smallint 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set facebook_id
     *
     * @param bigint $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;
    }

    /**
     * Get facebook_id
     *
     * @return bigint 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set category_id
     *
     * @param smallint $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
    }

    /**
     * Get category_id
     *
     * @return smallint 
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set pass
     *
     * @param string $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set body
     *
     * @param text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set location
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date_login
     *
     * @param datetime $dateLogin
     */
    public function setDateLogin($dateLogin)
    {
        $this->date_login = $dateLogin;
    }

    /**
     * Get date_login
     *
     * @return datetime 
     */
    public function getDateLogin()
    {
        return $this->date_login;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * Get votes
     *
     * @return integer 
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set visits
     *
     * @param integer $visits
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;
    }

    /**
     * Get visits
     *
     * @return integer 
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Set is freelance
     *
     * @param integer $freelance
     */
    public function setFreelance($freelance)
    {
        $this->freelance = $freelance;
    }

    /**
     * Get is freelance
     *
     * @return integer 
     */
    public function getFreelance()
    {
        return $this->freelance;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set linkedin_url
     *
     * @param string $linkedinUrl
     */
    public function setLinkedinUrl($linkedinUrl)
    {
        $this->linkedin_url = $linkedinUrl;
    }

    /**
     * Get linkedin_url
     *
     * @return string 
     */
    public function getLinkedinUrl()
    {
        return $this->linkedin_url;
    }

    /**
     * Set twitter_url
     *
     * @param string $twitterUrl
     */
    public function setTwitterUrl($twitterUrl)
    {
        $this->twitter_url = $twitterUrl;
    }

    /**
     * Get twitter_url
     *
     * @return string 
     */
    public function getTwitterUrl()
    {
        return $this->twitter_url;
    }

    /**
     * Set forrst_url
     *
     * @param string $forrstUrl
     */
    public function setForrstUrl($forrstUrl)
    {
        $this->forrst_url = $forrstUrl;
    }

    /**
     * Get forrst_url
     *
     * @return string 
     */
    public function getForrstUrl()
    {
        return $this->forrst_url;
    }

    /**
     * Set github_url
     *
     * @param string $githubUrl
     */
    public function setGithubUrl($githubUrl)
    {
        $this->github_url = $githubUrl;
    }

    /**
     * Get github_url
     *
     * @return string 
     */
    public function getGithubUrl()
    {
        return $this->github_url;
    }

    /**
     * Set dribbble_url
     *
     * @param string $dribbbleUrl
     */
    public function setDribbbleUrl($dribbbleUrl)
    {
        $this->dribbble_url = $dribbbleUrl;
    }

    /**
     * Get dribbble_url
     *
     * @return string 
     */
    public function getDribbbleUrl()
    {
        return $this->dribbble_url;
    }

    /**
     * Set flickr_url
     *
     * @param string $flickrUrl
     */
    public function setFlickrUrl($flickrUrl)
    {
        $this->flickr_url = $flickrUrl;
    }

    /**
     * Get flickr_url
     *
     * @return string 
     */
    public function getFlickrUrl()
    {
        return $this->flickr_url;
    }

    /**
     * Set youtube_url
     *
     * @param string $youtubeUrl
     */
    public function setYoutubeUrl($youtubeUrl)
    {
        $this->youtube_url = $youtubeUrl;
    }

    /**
     * Get youtube_url
     *
     * @return string 
     */
    public function getYoutubeUrl()
    {
        return $this->youtube_url;
    }

    /**
     * Get stackoverflow_url
     *
     * @return string 
     */
    public function getStackoverflowUrl()
    {
        return $this->stackoverflow_url;
    }

    /**
     * Set stackoverflow_url
     *
     * @param string $stackoverflowUrl
     */
    public function setStackoverflowUrl($stackoverflowUrl)
    {
        $this->stackoverflow_url = $stackoverflowUrl;
    }

    /**
     * Set vimeo_url
     *
     * @param string $vimeoUrl
     */
    public function setVimeoUrl($vimeoUrl)
    {
        $this->vimeo_url = $vimeoUrl;
    }

    /**
     * Get vimeo_url
     *
     * @return string 
     */
    public function getVimeoUrl()
    {
        return $this->vimeo_url;
    }

    /**
     * Set delicious_url
     *
     * @param string $deliciousUrl
     */
    public function setDeliciousUrl($deliciousUrl)
    {
        $this->delicious_url = $deliciousUrl;
    }

    /**
     * Get delicious_url
     *
     * @return string 
     */
    public function getDeliciousUrl()
    {
        return $this->delicious_url;
    }

    /**
     * Set pinboard_url
     *
     * @param string $pinboardUrl
     */
    public function setPinboardUrl($pinboardUrl)
    {
        $this->pinboard_url = $pinboardUrl;
    }

    /**
     * Get pinboard_url
     *
     * @return string 
     */
    public function getPinboardUrl()
    {
        return $this->pinboard_url;
    }
    
    /**
     * Set itunes_url
     *
     * @param string $itunesUrl
     */
    public function setItunesUrl($itunesUrl)
    {
        $this->itunes_url = $itunesUrl;
    }

    /**
     * Get itunes_url
     *
     * @return string 
     */
    public function getItunesUrl()
    {
        return $this->itunes_url;
    }
    
    /**
     * Set android_url
     *
     * @param string $androidUrl
     */
    public function setAndroidUrl($androidUrl)
    {
        $this->android_url = $androidUrl;
    }

    /**
     * Get android_url
     *
     * @return string 
     */
    public function getAndroidUrl()
    {
        return $this->android_url;
    }

    /**
     * Set can_contact
     *
     * @param string $canContact
     */
    public function setCanContact($canContact)
    {
        $this->can_contact = $canContact;
    }

    /**
     * Get ref_id
     *
     * @return string 
     */
    public function getRefId()
    {
        return $this->ref_id;
    }

    /**
     * Set ref_id
     *
     * @param string $refId
     */
    public function setRefId($refId)
    {
        $this->ref_id = $refId;
    }

    /**
     * Get can_contact
     *
     * @return string 
     */
    public function getCanContact()
    {
        return $this->can_contact;
    }

    /**
     * Set total_logins
     *
     * @param integer $totalLogins
     */
    public function setTotalLogins($totalLogins)
    {
        $this->total_logins = $totalLogins;
    }

    /**
     * Get total_logins
     *
     * @return integer 
     */
    public function getTotalLogins()
    {
        return $this->total_logins;
    }

    /**
     * Set avatar_type
     *
     * @param integer $avatarType
     */
    public function setAvatarType($avatarType)
    {
        $this->avatar_type = $avatarType;
    }

    /**
     * Get avatar_type
     *
     * @return integer 
     */
    public function getAvatarType()
    {
        return $this->avatar_type;
    }

    /**
     * Set ip
     */
    public function setIp()
    {
		// http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return integer 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get gravatar id
     *
     * @return string 
     */
	public function getGravatarId(){
		return md5( $this->getEmail() );
	}

    /**
     * Get avatar from gravatar
     *
     * @return string 
     */
    public function getAvatar($size = 'normal')
    {
		switch( $size ){
			case 'mini':
				$size_int = 24;
				$size_fb = 'small';
				break;
				
			case 'normal':
				$size_int = 48;
				$size_fb = 'square';
				break;
				
			case 'bigger':
				$size_int = 80;
				$size_fb = 'normal';
				break;
		}

		switch( $this->getAvatarType() ){
			case AVATAR_TWITTER:
				$url = 'http://api.twitter.com/1/users/profile_image/' . $this->getTwitterUrl() . '.json?size=' . $size;
				break;
				
			case AVATAR_FACEBOOK:
				$url = 'http://graph.facebook.com/' . $this->getFacebookId() . '/picture?type=' . $size_fb;
				break;
				
			default:
				$url = "http://www.gravatar.com/avatar/" . $this->getGravatarId() . "?s=" . $size_int . '&d=http://dir.betabeers.com/bundles/applicationanuncios/images/default_avatar.png';
				break;
		}
	
        return $url;
    }

}