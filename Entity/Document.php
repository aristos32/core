<?php

namespace Aristos\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="Aristos\CoreBundle\Entity\Repository\DocumentRepository")
 * @ORM\Table(name="document")
 * @ORM\HasLifecycleCallbacks
 * 
 * documents uploaded by users
 * 
 * tutorial: http://symfony.com/doc/current/cookbook/doctrine/file_uploads.html
 */
class Document {

    //needed in getCKEditorAssetPath, to store path in text, for display images in ivoryCKEditor
    private $moduleName;
    private $imagesTypes = array(
        'image/gif' => 'gif',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    );
    private $nonImagesTypes = array(
        'application/pdf' => 'doc',
        'application/x-pdf' => 'doc',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'doc',
        'application/vnd.oasis.opendocument.text' => 'doc'
    );

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * initial name
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * new hashed name
     */
    private $path;

    /**
     * @Assert\File(maxSize="6M")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="Aristos\CoreBundle\Entity\User", inversedBy="documents")
     *
     * user that uploaded this document
     *
     * */
    private $user;

    /**
     * temp variable
     *
     * @var unknown
     */
    private $temp;

    /**
     * new temp filename, after resize
     *
     * @var unknown
     */
    private $newTemp;

    /**
     * md5 file name
     * 
     * @var string
     */
    private $filename;

    /**
     * each document must belong to an album
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @var string album name
     */
    private $album;
    private $maxAllowedSize;
    private $maxAllowedWidth;
    private $resizeNewWidth;

    //set some default values
    public function __construct($maxAllowedSize, $maxAllowedWidth, $resizeNewWidth, $brand) {
        $this->maxAllowedSize = $maxAllowedSize;
        $this->maxAllowedWidth = $maxAllowedWidth;
        $this->resizeNewWidth = $resizeNewWidth;
        $this->moduleName = "/$brand";
    }

    /**
     * from filemane, create the absolute path
     * 
     * @param unknown $filename
     * @return Ambigous <NULL, string>
     */
    public function getAbsolutePath($filename) {
        $fullpath = $this->makeUploadPath($filename);

        return null === $this->path ? null : $fullpath . '/' . $this->path;
    }

    public function getWebPath() {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir() {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set user
     *
     * @param \Aristos\CoreBundle\Entity\User $user
     * @return Document
     */
    public function setUser(\Aristos\CoreBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Aristos\CoreBundle\Entity\User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set isProfilePhoto
     *
     * @param boolean $isProfilePhoto
     * @return Document
     */
    public function setIsProfilePhoto($isProfilePhoto) {
        $this->isProfilePhoto = $isProfilePhoto;

        return $this;
    }

    /**
     * Get isProfilePhoto
     *
     * @return boolean 
     */
    public function getIsProfilePhoto() {
        return $this->isProfilePhoto;
    }

    /**
     * Set collectionName
     *
     * @param string $collectionName
     * @return Document
     */
    public function setCollectionName($collectionName) {
        $this->collectionName = $collectionName;

        return $this;
    }

    /**
     * Get collectionName
     *
     * @return string 
     */
    public function getCollectionName() {
        return $this->collectionName;
    }

    /**
     * Sets file. called by form->handlerequest
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;

        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set album
     *
     * @param string $album
     * @return Document
     */
    public function setAlbum($album) {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return string
     */
    public function getAlbum() {
        return $this->album;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        //resize image
        //$this->imageResize();

        if (null !== $this->getFile()) {
            //generate a unique name
            $this->filename = md5(uniqid(mt_rand(), true));
            $this->path = $this->filename . '.' . $this->getFile()->guessExtension();

            $this->setName($this->getFile()->getClientOriginalName());
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        //full upload path
        $fullpath = $this->makeUploadPath($this->filename);

        //name implies the path also
        //$this->setName($this->path);
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        // move takes the target directory and then the target filename to move to
        $this->getFile()->move($fullpath . '/', $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            echo $this->temp;
            exit;
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * 
     * @ORM\PostRemove()
     * 
     * remove a file from disk. Called automatically after entity is removed.
     */
    public function removeUpload() {

        $file = $this->getAbsolutePath($this->path);

        if ($file) {
            unlink($file);
        }
    }

    /**
     * convert an md5 hash to a full upload path
     * i.e c23ebeb2b74d55037e885324efc5cbca -> 
     * /travelbuddies/web/uploads/documents/9b/cd/5f/5c/7f/52/1a/a5/1f/45/ae/89/73/9f/75/8c/9bcd5f5c7f521aa51f45ae89739f758c.png
     * 
     * @param string $hash md5
     * @param string $storageName
     * @return boolean|string
     */
    public function makeUploadPath($hash, $storageName = '') {
        $storageName = preg_replace('/[^a-z0-1\-\_]/', '', $storageName);

        $path = $this->hashToPath($hash);
        //echo $path;exit;

        $fullPath = $this->getUploadRootDir()
                . ($storageName ? '/' . $storageName : '')
                . '/' . $path;

        if (!is_dir($fullPath)) {
            if (!@mkdir($fullPath, 0777, true)) {
                return false;
            }
        }

        return $fullPath;
    }

    /**
     * convert a hash to a path structure
     * 
     * @param unknown $hash
     * @return boolean|string
     */
    public function hashToPath($hash) {
        if (!preg_match('/[a-z0-9]+/', $hash, $m)) {
            return false;
        }
        if (strlen($m[0]) != 32) {
            return false;
        }
        $hash = $m[0];
        preg_match_all('/\w{2}/', $hash, $m);
        $path = implode('/', $m[0]);
        //echo $path;exit;

        return $path;
    }

    /**
     * path relative to assets 'web' folder
     * for displaying assets in twig
     * 
     * @return string
     */
    public function getAssetPath() {
        $name = pathinfo($this->getPath(), PATHINFO_FILENAME);
        $fullpath = $this->hashToPath($name);

        return 'uploads/documents/' . $fullpath . '/' . $this->getPath();
    }

    /**
     * DEV ENV
     * 
     * asset path for displaying images in ivory ckeditor
     *
     * @return string
     */
    public function getCKEditorDevAssetPath() {
        return $this->moduleName . '/web/' . $this->getAssetPath();
    }

    /**
     * PROD ENV
     * asset path for displaying images in ivory ckeditor
     *
     * @return string
     */
    public function getCKEditorProdAssetPath() {
        $name = pathinfo($this->getPath(), PATHINFO_FILENAME);
        $fullpath = $this->hashToPath($name);

        return '/uploads/documents/' . $fullpath . '/' . $this->getPath();
    }

    /**
     * is allowed image type
     * 
     * @return boolean
     */
    private function isImage() {
        
        $type = $_FILES ['upload'] ['type'];

        if (isset($this->imagesTypes[$type])) {
            return true;
        }
        else{
           return false;
        }
    }
    
    /**
     * UNTESTED!!!
     * 
     * is allowed file type
     * 
     * @return boolean
     */
    private function isAllowedFileType() {
        
        $allowed = array_merge($this->imagesTypes, $this->nonImagesTypes);
        $type = $_FILES ['upload'] ['type'];

        if (isset($$allowed[$type])) {
            return true;
        }
        else{
           return false;
        }
    }

    /**
     * resize file
     *
     * http://www.w3bees.com/2013/03/resize-image-while-upload-using-php.html
     */
    private function imageResize() {
        
        
        
        //tmp name may change after resize
        $this->newTemp = $_FILES['upload']['tmp_name'];

        /* Get original image x y */
        list ( $width, $height ) = getimagesize($_FILES ['upload'] ['tmp_name']);

        $size = $_FILES['upload']['size'];

        if ($size > $this->maxAllowedSize || $width > $this->maxAllowedWidth) {
            //echo $size; exit;
            $this->newTemp = $this->resize($this->resizeNewWidth);
            if ($this->newTemp == false) {
                die('No allowed File Type' . ': ' . $type);
            }
        }
        
        return true;
    }

    /**
     * when uploading from ckeditor,need to create the object manually
     */
    public function createUploadedFile() {
        
        if($this->isImage()==false){
            return false;
        }
        
        //resize if too big    	
        $this->imageResize();

        $uploadedFile = new UploadedFile($this->newTemp, $_FILES['upload']['name'], $_FILES['upload']['type'], filesize($this->newTemp), false, true);
        $this->setFile($uploadedFile);
        
        return true;
    }

    /**
     * image resize - new width is set by ratio
     * @param unknown $newwidth
     *
     * @return string
     */
    private function resize($newwidth) {
        /* Get original image x y */
        list ( $width, $height ) = getimagesize($_FILES ['upload'] ['tmp_name']);
        /* calculate new image size with ratio */
        $newheight = ($height / $width) * $newwidth;

        /* new file name */
        $path = '/tmp/' . $width . 'x' . $height . '_' . $_FILES ['upload'] ['name'];
        /* read binary data from image file */
        $imgString = file_get_contents($_FILES ['upload'] ['tmp_name']);
        /* create image from string */
        $image = imagecreatefromstring($imgString);
        $tmp = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        /* Save image */
        $type = $_FILES ['upload'] ['type'];

        switch ($type) {
            case 'image/jpeg' :
            case 'image/jpg' :
                imagejpeg($tmp, $path, 100);
                break;
            case 'image/png' :
                imagepng($tmp, $path, 0);
                break;
            case 'image/gif' :
                imagegif($tmp, $path);
                break;
            default :
                return false;
        }

        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($tmp);

        return $path;
    }

}
