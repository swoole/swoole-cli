import {show_git_branch_list} from './Index/git-branch-box.js'

import {show_extension_list} from './Index/extensions-box.js'
import {show_controller} from './Index/controller.js'

export default () => {
    show_git_branch_list()
    show_controller()
    show_extension_list()
}
