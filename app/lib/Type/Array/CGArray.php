<?php

namespace App\Lib\Type\Array;


class CGArray implements CGArrayInterface
{
    protected array $array = [];
    private int $position = 0;

    public function __construct($array = [])
    {
        if (empty($array)) {
            return false;
        }
        $this->array = $array;
        return $this;
    }

    /**
     * 取得陣列中的鍵值。
     *
     * @param mixed|null $filter_value 選填，篩選的值。
     * @param bool       $strict       是否使用嚴格比較。
     *
     * @return array
     */
    public function getKeys(mixed $filter_value = null, bool $strict = false)
    {
        if($filter_value === null) return array_keys($this->array);
        return array_keys($this->array, $filter_value, $strict);
    }

    public function getValues()
    {
        $arr = [];
        foreach ($this->array as $value) {
            $arr[] = $value;
        }
        $this->array = $arr;
        return $this;
    }

    /**
     * 搬移陣列中的參數至新的參數
     */
    public function shiftKeytoNewKey($oldKey, $newKey, bool $deleteold = true)
    {
        if ($this->IsEmpty()) {
            return false;
        }
        $this->Set($newKey, $this->Get($oldKey));
        if ($deleteold) {
            $this->Delete($oldKey, true);
        }
        return $this;
    }

    public function IsEmpty(): bool
    {
        return empty($this->array);
    }

    public function Set($key, $Mixed): void
    {
        $this->array[$key] = $Mixed;
    }

    public function Get($Index)
    {
        return $this->array[$Index];
    }

    public function Delete($Key, $force = false)
    {
        if (!$force) {
            if (empty($this->array[$Key])) {
                return null;
            }
        }
        unset($this->array[$Key]);
        return $this;
    }

    public function Merge($string = "")
    {
        if (!empty($string)) {
            return implode($string, $this->array);
        }
        return implode($this->array);
    }

    public function getLast()
    {
        return $this->array[$this->Size() - 1];
    }

    public function Size(): int
    {
        return count($this->array);
    }

    public function getLastObject()
    {
        $k = 0;
        foreach ($this->array as $item) {
            if (count($this->array) === $k) {
                return $item;
            }
            $k++;
        }
        return null;
    }

    public function splitLastObjectFromNumber($number = 1)
    {
        $k = 0;
        $count = $this->count();
        foreach ($this->array as $key => $item) {
            if ($k >= $count - $number) {
                $this->Delete($key);
            }
            $k++;
        }
        return $this;
    }

    public function count(): int
    {
        return count($this->array);
    }

    public function splitFirstObjectFromNumber($number = 1)
    {
        $k = 0;
        foreach ($this->array as $key => $item) {
            if ($k <= $number) {
                $this->Delete($key);
            }
            $k++;
        }
        return $this;
    }

    /**
     * 取得陣列中的第 0 個元素。
     *
     * @return mixed
     */
    public function getFirst()
    {
        return $this->array[0];
    }

    public function getFirstObject(): CGArray|null
    {
        $array_key_first = array_key_first($this->array);

        if ($array_key_first !== null) {
            return new CGArray($this->array[$array_key_first]);
        }
        return null;
    }

    public function getFirstObjectKey()
    {
        $array_key_first = array_key_first($this->array);
        if (!empty($array_key_first)) {
            return $array_key_first;
        }
    }

    public function getLastObjectKey()
    {
        $array_key_last = array_key_last($this->array);
        if (!empty($array_key_last)) {
            return $array_key_last;
        }
    }

    public function Add($Mixed): void
    {
        $this->array[] = $Mixed;
    }

    public function AddCallBack($Mixed): CGArray
    {
        $this->array[] = $Mixed;
        return $this;
    }

    public function RemoveCallBack($Index): CGArray
    {
        array_splice($this->array, $Index, 1);
        return $this;
    }

    public function IndexOf($Value): bool|int|string
    {
        return array_search($Value, $this->array, true);
    }

    /**
     * @param $Index
     *
     * @return bool|CGArray
     */
    public function GetValuetoCGArray($Index): bool|CGArray
    {
        if (!is_array($this->array[$Index])) {
            return false;
        }
        return new CGArray($this->array[$Index]);
    }

    public function hasKey($key)
    {
        if (@empty($this->Get($key))) {
            return false;
        }
        return true;
    }

    public function Contains($Mixed): bool
    {
        return in_array($Mixed, $this->array, true);
    }

    public function searchRemove($Mixed)
    {
        $this->Remove($this->search($Mixed));
        return $this;
    }

    public function Remove($Index): void
    {
        array_splice($this->array, $Index, 1);
    }

    public function search($Mixed): int
    {
        return array_search($Mixed, $this->array, true);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }

    /**
     * @param array $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    public function toArray(): array
    {

        return $this->array;
    }

    public function toPath(): CGPath
    {
        return new CGPath($this->array);
    }

    /**
     * ArrayAccess 接口的實現方法
     */
    public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->Get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->Add($value);
        } else {
            $this->Set($offset, $value);
        }
    }

    public function offsetUnset($offset): void
    {
        $this->Delete($offset);
    }

    public function resort()
    {
        $this->array = $this->array_resort($this->array);
        return $this;
    }

    public function array_resort(array $array, int $offset = -1, int $k = 0): array
    {
        $arr = [];
        foreach ($array as $value) {
            if ($offset === (-1)) {
                $arr[$k] = $value;
                $k++;
            } else {
                if ($k <= count($array) - $offset - 1) {
                    $arr[$k] = $value;
                    $k++;
                }
            }
        }
        return $arr;
    }

    public function array_decode(array $array): string
    {
        $string = '';
        for ($i = 0; $i <= count($array) - 1; $i++) {
            if ($i !== 0) {
                $string .= '/';
            }
            for ($y = 0; $y <= count($array[$i]) - 1; $y++) {
                if ($y === count($array[$i]) - 1) {
                    $string .= $array[$i][$y];
                } else {
                    $string .= $array[$i][$y] . ':';
                }
            }
        }
        return $string;
    }

    public function array_encode(string $string): array
    {
        $array = [];
        $b = explode('/', $string);
        for ($i = 0; $i <= count($b) - 1; $i++) {
            $d = [];
            $c = explode(':', $b[$i]);
            for ($y = 0; $y <= count($c) - 1; $y++) {
                $d[$y] = $c[$y];
            }
            $array[$i] = $d;
        }

        return $array;
    }

    public function array_diffs(array $arr1, array $arr2, bool $result = false, bool $notfoundmsg = false)
    {
        $arr = [];
        foreach ($arr1 as $key => $value) {
            if (!empty($arr2[$key])) {
                if (@$arr1[$key] !== $arr2[$key]) {
                    $r = true;
                } else {
                    $r = false;
                }
                $arr[] = $r;
            }
            if ($notfoundmsg === true) {
                echo $key . ' 未找相關指標名稱。<br>';
            }

        }
        //if($e>0) return false;
        if ($result === true) {
            return $arr;
        }
        return true;
    }

    public function array_splice_key(
        array $keyrows = null,
        bool $nametokey = false,
        bool $result = false,
        bool $keyint = false,
    ) {
        $arr = [];
        if (is_array($keyrows)) {
            if ($nametokey === true) {
                $int_arr = $this->array_keytovalue($this->array);
                foreach ($keyrows as $v) {
                    if ($result === true) {
                        $arr[] = $this->array[$int_arr[$v]];
                    }
                    unset($this->array[$int_arr[$v]]);
                }
            } else {
                foreach ($this->array as $key => $value) {
                    for ($i = 0; $i <= count($keyrows) - 1; $i++) {
                        if ($key === $keyrows[$i]) {
                            if ($result === true) {
                                $arr[] = $this->array[$key];
                            }
                            unset($this->array[$key]);
                        }
                    }
                }
            }
            if ($keyint === true) {
                $this->array = $this->array_resort($this->array);
            }
            if ($result === true) {
                return $arr;
            }
            return true;
        }

        return false;
    }

    public function array_keytovalue(array $array, bool $value = false, string $prefix = ':')
    {
        $arr = [];
        $k = 0;
        foreach ($array as $key => $v) {
            if ($value === true) {
                $arr[$k] = $key . $prefix . $v;
            } else {
                $arr[$k] = $key;
            }
            $k++;
        }
        return $arr;
    }

    /**
     * Iterator 接口的實現方法
     */
    public function current(): mixed
    {
        return $this->array[$this->key()];
    }

    public function key(): mixed
    {
        $keys = array_keys($this->array);
        return $keys[$this->position] ?? null;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        $keys = array_keys($this->array);
        return isset($keys[$this->position]);
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
