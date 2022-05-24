<?php
/**
 *
 * Copyright (C) 2020 - 2022 | Matthew Jordan
 *
 * This program is private software. You may not redistribute this software, or
 * any derivative works of this software, in source or binary form, without
 * the express permission of the owner.
 *
 * @author sylvrs
 */
declare(strict_types=1);

namespace libgame\interfaces;

interface Updatable {

	/**
	 * This method should be called whenever the object needs/wants to be updated.
	 *
	 * @return void
	 */
	public function update(): void;

}