<?php

namespace GlpiPlugin\Favorites;

use CommonGLPI;
use DbUtils;
use Glpi\Application\View\TemplateRenderer;
use ProfileRight;
use Session;

class Profile extends \Profile
{
    public static $rightname = 'profile';

    /**
     * @param CommonGLPI $item
     * @param int $withtemplate
     *
     * @return string
     */
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item->getType() == 'Profile') {
            return self::createTabEntry(__('Favorite', PLUGIN_FAVORITES));
        }
        return '';
    }

    /**
     * @return string
     */
    public static function getIcon()
    {
        return Favorite::getIcon();
    }

    /**
     * @param CommonGLPI $item
     * @param int $tabnum
     * @param int $withtemplate
     *
     * @return bool
     */
    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if (!$item instanceof \Profile || !self::canView()) {
            return false;
        }

        $profile = new \Profile();
        $profile->getFromDB($item->getID());

        $rights = self::getAllRights();

        $twig = TemplateRenderer::getInstance();
        $twig->display('@favorites/profile.html.twig', [
            'id' => $item->getID(),
            'profile' => $profile,
            'title' => self::getTypeName(Session::getPluralNumber()),
            'rights' => $rights,
        ]);

        return true;
    }

    /**
     * @return array[]
     */
    static function getAllRights()
    {
        return [
            [
                'itemtype' => Favorite::class,
                'label' => Favorite::getTypeName(),
                'field' => Favorite::$rightname,
                'rights' => \Profile::getRightsFor(Favorite::class)
            ]
        ];
    }

    /**
     * @param $profiles_id
     * @return void
     */
    static function showForProfile($profiles_id = 0)
    {
        $profile = new Profile();
        $profile->getFromDB($profiles_id);

        TemplateRenderer::getInstance()->display('@favorites/profile.html.twig', [
            'can_edit' => self::canUpdate(),
            'profile' => $profile,
            'rights' => self::getAllRights()
        ]);
    }

    /**
     * @param $profile_id
     */
    public static function createFirstAccess($profile_id)
    {
        self::addDefaultProfileInfos(
            $profile_id,
            [Favorite::$rightname => ALLSTANDARDRIGHT],
            true
        );
    }

    /**
     * @param $profiles_id
     * @param $rights
     * @return void
     */
    public static function addDefaultProfileInfos($profiles_id, $rights, $drop_existing = false)
    {
        $profileRight = new ProfileRight();

        $dbu = new DbUtils();
        foreach ($rights as $right => $value) {
            if ($dbu->countElementsInTable(
                    'glpi_profilerights',
                    ['profiles_id' => $profiles_id, 'name' => $right]
                ) && $drop_existing) {
                $profileRight->deleteByCriteria(['profiles_id' => $profiles_id, 'name' => $right]);
            }
            if (!$dbu->countElementsInTable(
                'glpi_profilerights',
                [
                    'profiles_id' => $profiles_id,
                    'name' => $right
                ]
            )) {
                $myright['profiles_id'] = $profiles_id;
                $myright['name'] = $right;
                $myright['rights'] = $value;
                $profileRight->add($myright);

                //Add right to the current session
                $_SESSION['glpiactiveprofile'][$right] = $value;
            }
        }
    }

    /**
     * Initialize profiles
     */
    public static function initProfile()
    {
        global $DB;
        $profile = new self();
        $dbu = new DbUtils();
        //Add new rights in glpi_profilerights table
        foreach ($profile->getAllRights() as $data) {
            if ($dbu->countElementsInTable(
                    'glpi_profilerights',
                    ['name' => $data['field']]
                ) == 0) {
                ProfileRight::addProfileRights([$data['field']]);
            }
        }

        $it = $DB->request([
            'FROM' => 'glpi_profilerights',
            'WHERE' => [
                'profiles_id' => $_SESSION['glpiactiveprofile']['id'],
                'name' => ['LIKE', '%' . Favorite::$rightname . '%'],
            ],
        ]);
        foreach ($it as $prof) {
            if (isset($_SESSION['glpiactiveprofile'])) {
                $_SESSION['glpiactiveprofile'][$prof['name']] = $prof['rights'];
            }
        }
    }

    /**
     * @return void
     */
    public static function removeRightsFromSession()
    {
        foreach (self::getAllRights() as $right) {
            if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
                unset($_SESSION['glpiactiveprofile'][$right['field']]);
            }
        }
    }
}
