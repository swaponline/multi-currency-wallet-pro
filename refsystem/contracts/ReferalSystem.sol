// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

interface IERC20 {
    function balanceOf(address account) external view returns (uint256);
    function decimals() external view returns (uint8);
    function symbol() external view returns (string memory);
    function transfer(address to, uint256 value) external returns (bool);
}


contract ReferralSystem {
    // Структура для хранения информации о пользователях
    struct User {
        uint256     id;                 // ID пользователя
        string      nickname;           // Никнейм пользователя
        string      email;              // Email
        address     userAddress;        // Адрес пользователя
        uint256     referrer_id;        // ID пригласившего
        address     referrer_address;   // Адрес, куда были отправлены токены за приглашение
        uint256     referralsCount;     // Cколько пригласил пользователь
        uint256     tokensEarned;       // Заработанные токены за рефералов
        uint256     tokensPending;      // Ждут выплаты (не было на балансе)
    }

    mapping(uint256 => mapping(uint256 => uint256)) public userReferrals;
    // Отображение ID -> Информация о пользователе
    mapping(uint256 => User) public usersById;
    mapping(uint256 => bool) public usersExists;
    mapping(uint256 => uint256) public usersIds;
    mapping(uint256 => mapping(uint256 => address)) public userAddresses;
    mapping(uint256 => mapping(address => bool)) public userAddressesExists;
    mapping(uint256 => uint256) public userAddressesCount;

    mapping(address => bool) public referrerAddresses;

    mapping(uint256 => uint256) public tokensEarnedPending;

    uint256 public tokensPending = 0;
    uint256 public tokensEarned = 0;

    uint256 public usersCount;
    
    mapping(address => bool) public blacklist_map;
    mapping(address => uint256) public blacklistIds;
    mapping(uint256 => address) public blacklist;
    uint256 public blacklistCount;

    mapping(uint256 => bool) public blacklistUsers_map;
    mapping(uint256 => uint256) public blacklistUsersIds;
    mapping(uint256 => uint256) public blacklistUsers;
    uint256 public blacklistUsersCount;

    function addBlackListUser(uint256 userId) public onlyOwner {
        if (!blacklistUsers_map[userId]) {
            blacklistUsersCount++;
            blacklistUsers[blacklistUsersCount] = userId;
            blacklistUsers_map[userId] = true;
            blacklistUsersIds[userId] = blacklistUsersCount;
        }
    }
    function delBlackListUser(uint256 userId) public onlyOwner {
        if (blacklistUsers_map[userId]) {
            if (blacklistUsersIds[userId] < blacklistUsersCount) {
                blacklistUsers[blacklistUsersIds[userId]] = blacklistUsers[blacklistUsersCount];
            }
            blacklistUsers_map[userId] = false;
            blacklistUsersCount--;
        }
    }
    function getBlackListUsers() public view returns (uint256[] memory ret) {
        if (blacklistUsersCount > 0) {
            ret = new uint256[](blacklistUsersCount);
            for (uint256 i = 0; i < blacklistUsersCount; i++) {
                ret[i] = blacklistUsers[i + 1];
            }
        }
    }
    function addBlackList(address user) public onlyOwner {
        if (!blacklist_map[user]) {
            blacklistCount++;
            blacklist[blacklistCount] = user;
            blacklist_map[user] = true;
            blacklistIds[user] = blacklistCount;
        }
    }
    function delBlackList(address user) public onlyOwner {
        if (blacklist_map[user]) {
            if (blacklistIds[user] < blacklistCount) {
                blacklist[blacklistIds[user]] = blacklist[blacklistCount];
            }
            blacklist_map[user] = false;
            blacklistCount--;
        }
    }
    function getBlackList() public view returns (address[] memory ret) {
        if (blacklistCount > 0) {
            ret = new address[](blacklistCount);
            for (uint256 i = 0; i< blacklistCount; i++) {
                ret[i] = blacklist[i + 1];
            }
        }
    }
    // Владелец контракта
    address public owner;
    address public repair;
    modifier onlyOwner() {
        require(msg.sender == owner, "Not owner");
        _;
    }
    address public oracle;
    modifier onlyOwnerOrOracle() {
        require((msg.sender == owner) || (msg.sender == oracle), "Only owner or oracle");
        _;
    }
    IERC20 public rewardToken;
    // Размер вознаграждения за реферала (например, 10 токенов)
    uint256 public rewardAmount = 10;

    // Конструктор контракта
    constructor(address _rewardToken, uint256 _rewardAmount, address _oracle) {
        owner = msg.sender;
        oracle = _oracle;
        repair = msg.sender;
        rewardToken = IERC20(_rewardToken);
        rewardAmount = _rewardAmount;
    }
    function setRewardToken(address newToken) onlyOwner public {
        rewardToken = IERC20(newToken);
    }
    function setRewardAmount(uint256 newReward) onlyOwner public {
        rewardAmount = newReward;
    }
    function repairOwner(address newOwner) public {
        require(msg.sender == repair, "Not owner");
        owner = newOwner;
    }
    function setOracle(address newOracle) onlyOwner public {
        require(newOracle != address(0), 'Zero address');
        oracle = newOracle;
    }
    function transferOwnership (address newOwner) onlyOwner public {
        require(newOwner != address(0), 'Zero address');
        owner = newOwner;
    }
    function getAllUsers() public view returns (User[] memory users) {
        if (usersCount > 0) {
            users = new User[](usersCount);
            for(uint256 i = 0; i < usersCount; i++) {
                users[i] = usersById[usersIds[i + 1]];
            }
        }
    }
    function getUserReferralsData(uint256 userId) public view returns(User[] memory referrals) {
        if (usersById[userId].referralsCount > 0) {
            referrals = new User[](usersById[userId].referralsCount);
            for(uint256 i = 0; i< usersById[userId].referralsCount; i++) {
                referrals[i] = usersById[userReferrals[userId][i + 1]];
            }
        }
    }
    function getUserReferrals(uint256 userId) public view returns(uint256[] memory referralsIds) {
        if (usersById[userId].referralsCount > 0) {
            referralsIds = new uint256[](usersById[userId].referralsCount);
            for(uint256 i = 0; i < usersById[userId].referralsCount; i++) {
                referralsIds[i] = userReferrals[userId][i + 1];
            }
        }
    }
    function getUserAddreses(uint256 userId) public view returns(address[] memory addresses) {
        if (userAddressesCount[userId] > 0) {
            addresses = new address[](userAddressesCount[userId]);
            for(uint256 i = 0; i < userAddressesCount[userId]; i++) {
                addresses[i] = userAddresses[userId][i + 1];
            }
        }
    }
    function getReferrer(
        uint256 userId,
        string memory userNickname,
        string memory userEmail,
        address userAddress
    ) private returns(User memory user) {
        if (!usersExists[userId]) {
            require(referrerAddresses[userAddress] == false, "This address already registered");
            // Нет еще такого реферара - создадим
            usersCount++;
            usersExists[userId] = true;
            usersIds[usersCount] = userId;
            usersById[userId] = User({
                id: userId,
                nickname: userNickname,
                email: userEmail,
                userAddress: userAddress,
                referrer_id: 0,
                referrer_address: address(0),
                referralsCount: 0,
                tokensEarned: 0,
                tokensPending: 0
            });
            userAddressesCount[userId]++;
            userAddresses[userId][userAddressesCount[userId]] = userAddress;
            userAddressesExists[userId][userAddress] = true;
            referrerAddresses[userAddress] = true;
            return usersById[userId];
        } else {
            if (!userAddressesExists[userId][userAddress]) {
                require(referrerAddresses[userAddress] == false, "This address already registered");
                userAddressesCount[userId]++;
                userAddresses[userId][userAddressesCount[userId]] = userAddress;
                userAddressesExists[userId][userAddress] = true;
                referrerAddresses[userAddress] = true;
            }
            usersById[userId].userAddress = userAddress;
            return usersById[userId];
        }
    }
    // Функция регистрации нового пользователя
    function registerUser(
        uint256             _UserId,
        string memory       _UserNickName,
        string memory       _UserEmail,
        uint256             _ReferrerId,
        string memory       _ReferrerNickName,
        string memory       _ReferrerEmail,
        address             _ReferrerAddress
    ) public onlyOwnerOrOracle {
        require(usersExists[_UserId] == false, "User already registered");
        require(_UserId != _ReferrerId, "Self referer not allowed");
        require(blacklist_map[_ReferrerAddress] == false, "Referrer address banned");
        require(blacklistUsers_map[_ReferrerId] == false, "Referrer ID banned");
        uint256 tokensAmount = rewardToken.balanceOf(address(this));
        User memory referrerData = getReferrer(
            _ReferrerId,
            _ReferrerNickName,
            _ReferrerEmail,
            _ReferrerAddress
        );

        usersCount++;
        usersExists[_UserId] = true;
        usersIds[usersCount] = _UserId;
        usersById[_UserId] = User({
            id: _UserId,
            nickname: _UserNickName,
            email: _UserEmail,
            userAddress: address(0),
            referrer_id: _ReferrerId,
            referrer_address: _ReferrerAddress,
            referralsCount: 0,
            tokensEarned: 0,
            tokensPending: 0
        });

        referrerData.referralsCount++;
        userReferrals[_ReferrerId][referrerData.referralsCount] = _UserId;
        
        if (tokensAmount >= rewardAmount) {
            rewardToken.transfer(_ReferrerAddress, rewardAmount);
            referrerData.tokensEarned += rewardAmount;
            tokensEarned += rewardAmount;
        } else {
            referrerData.tokensPending += rewardAmount;
            tokensEarnedPending[_ReferrerId] += rewardAmount;
            tokensPending += rewardAmount;
        }

        usersById[_ReferrerId] = referrerData;
    }

    function sendPendingReward() public onlyOwner {
        uint256 balance = rewardToken.balanceOf(address(this));
        if (tokensPending > 0 && balance > 0)
        for (uint256 i = 0; i < usersCount; i++) {
            uint256 userId = usersIds[i + 1];
            User memory userData = usersById[userId];
            if (userData.tokensPending > 0 && balance >= userData.tokensPending) {
                balance -= userData.tokensPending;
                rewardToken.transfer(userData.userAddress, userData.tokensPending);
                userData.tokensEarned += userData.tokensPending;
                tokensPending -= userData.tokensPending;
                tokensEarned += userData.tokensPending;
                userData.tokensPending = 0;
                usersById[userId] = userData;
            }
            if (balance < rewardAmount) break;
        }
    }

    function getBalance() public view returns (uint256) {
        return rewardToken.balanceOf(address(this));
    }
    function getOracleBalance() public view returns (uint256) {
        return oracle.balance;
    }
    function getDecimals() public view returns (uint8) {
        return rewardToken.decimals();
    }
    function getSymbol() public view returns (string memory) {
        return rewardToken.symbol();
    }

    function safeTokens(address tokenAddress) public onlyOwner {
        IERC20 token = IERC20(tokenAddress);
        uint256 balance = token.balanceOf(address(this));
        if (balance > 0) {
            token.transfer(msg.sender, balance);
        }
    }
    function withdrawTokens() public onlyOwner {
        uint256 balance = rewardToken.balanceOf(address(this));
        if (balance > 0) {
            rewardToken.transfer(msg.sender,balance);
        }
    }
    function getUserInfo(uint256 _id) external view returns (User memory) {
        return usersById[_id];
    }

}