<?php

namespace Ghalf;

/**
 * 内置常量
 */
final class Consts {

    /**
     * 基本信息
     */
    const VERSION = '0.8.0';
    const ENVIRON = 'develop';

    /**
     * 基本设定
     */
    const GC = 'global';

    /**
     * Error
     */
    const ERROR_CONTROLLER = 'Error';
    const ERROR_ACTION     = 'error';

    /**
     * 类名后缀式
     */
    const SUFIXX_CONTROLLER = '\\%s%sController';
    const SUFIXX_MODEL      = '%s%sModel';
    const SUFIXX_ACTION     = '%s%sAction';
    const SUFIXX_PLUGIN     = '%s%sPlugin';

    /**
     * 类名前缀式
     */
    const PREFIX_CONTROLLER = '\\Controller%s%s';
    const PREFIX_MODEL      = 'Model%s%s';
    const PREFIX_ACTION     = 'action%s%s';
    const PREFIX_PLUGIN     = 'Plugin%s%s';

    /**
     * 抛错信息 401 - 409
     */
    const ERR_STARTUP_FAILED  = 0x190;
    const ERR_ROUTE_FAILED    = 0x191;
    const ERR_DISPATCH_FAILED = 0x192;
    const ERR_MODULE_404      = 0x193;
    const ERR_CONTROLLER_404  = 0x194;
    const ERR_ACTION_404      = 0x195;
    const ERR_VIEW_404        = 0x196;
    const ERR_CALL_FAILED     = 0x197;
    const ERR_AUTOLOAD_FAILED = 0x198;
    const ERR_TYPE_ERROR      = 0x199;

}
