<?php

/**
 * @secured
 * @loggedIn
 */
class LoggedPresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @loggedIn('You must be logged in')
 */
class LoggedWithCustomMessagePresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @role(user)
 */
class RolePresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @role(one,two,three)
 */
class InvalidRolePresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @role(one,two,three,message="You must be in role 'user'.")
 */
class InvalidRoleWithCustomMessagePresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @allowed(comments,view)
 */
class PermissionPresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @allowed(comments,edit)
 */
class InvalidPermissionPresenter extends JedenWeb\Application\UI\Presenter
{
}


/**
 * @secured
 * @allowed(comments,edit,message="You are not allowed to edit comments.")
 */
class InvalidPermissionWithCustomMessagePresenter extends JedenWeb\Application\UI\Presenter
{
}
