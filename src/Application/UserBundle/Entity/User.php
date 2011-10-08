<?php 

namespace Application\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="facebook_id", type="bigint")
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var text $body
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var string $location
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer $votes
     *
     * @ORM\Column(name="votes", type="integer")
     */
    private $votes;

    /**
     * @var integer $visits
     *
     * @ORM\Column(name="visits", type="integer")
     */
    private $visits;

    /**
     * @var integer $freelance
     *
     * @ORM\Column(name="freelance", type="integer")
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
     * @var string $can_contact
     *
     * @ORM\Column(name="can_contact", type="integer")
     */
    private $can_contact;

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
     * Set can_contact
     *
     * @param string $canContact
     */
    public function setCanContact($canContact)
    {
        $this->can_contact = $canContact;
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
     * Get avatar from gravatar
     *
     * @return string 
     */
    public function getAvatar($size = 50)
    {
        return "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $this->email ) ) ) . "?s=" . $size;
    }

}